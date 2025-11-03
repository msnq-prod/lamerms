# AdamRMS Agent Handbook

## Build, Lint & Test Commands
- **Bootstrap**  
  - Copy env vars: `cp .env.example .env`, then set `CONFIG_ROOTURL` (no trailing slash) and 64-char `CONFIG_AUTH_JWTKey`.  
  - Install PHP deps when developing locally: `composer install`. Docker builds run `composer install --no-dev` in the build stage (`Dockerfile`).
- **Run stack (Docker Compose)**  
  - Start core services: `docker compose up -d`.  
  - Include Mailpit for SMTP testing: `docker compose --profile notifications up -d`.  
  - Rebuild after dependency changes: `docker compose build app` (add `--no-cache` to force a clean composer layer).  
  - View logs: `docker compose logs app | tail`.
- **Migrations & Seeds (Phinx)**  
  - Status: `docker compose exec app php vendor/bin/phinx status -e production`.  
  - Apply migrations: `docker compose exec app php vendor/bin/phinx migrate -e production`.  
  - Rerun seeders used by `migrate.sh`:  
    ```bash
    docker compose exec app php vendor/bin/phinx seed:run -e production \
      -s PositionsSeeder -s DefaultUserSeeder -s ManufacturersSeeder \
      -s AssetCategorySeeder -s MaintenanceJobsStatusesSeeder
    ```  
  - Roll back to a specific migration: `docker compose exec app php vendor/bin/phinx rollback -e production -t <timestamp>`.
- **Lint & QA**  
  - PHP syntax check for a single file: `docker compose exec app php -l src/api/projects/list.php`.  
  - Render smoke test for a Twig template by compiling the PHP entrypoint (e.g. `php -l src/index.php`).  
  - Container health relies on `migrate.sh`; review start-up validation via `docker compose logs app`.
- **Automated tests**  
  - No PHPUnit (or other) test suite is committed. If you introduce one, run everything with `docker compose exec app php vendor/bin/phpunit` and target a single test with `--filter "<TestName>"`. Today validation is limited to linting, Phinx migrations, and manual UI/API checks.

## Architecture & Codebase Structure
- **Containers & Runtime**  
  - `docker-compose.yml` runs `app` (PHP 8.3 + Apache) and `db` (MariaDB 10.11); `mail` (Mailpit) is optional.  
  - `migrate.sh` is the container entrypoint: it validates env vars, runs Phinx migrations/seeds, and finally starts Apache. Environment flags such as `SEED_ON_START` and `DEV_MODE` adjust behaviour.
- **Source Layout (`src/`)**  
  - `common/` provides the bootstrap in `common/head.php` (loads Composer autoload, config, Twig, database) and `common/headSecure.php` (auth gate, analytics log). Shared libraries live under `common/libs/` (Auth, Config, Email, Search, Telemetry, bCMS helpers, Twig extensions).  
  - `api/` contains endpoint scripts that include `apiHead.php`/`apiHeadSecure.php`, use `$DBLIB` (MysqliDb) and `$AUTH`, and respond via the `finish()` helper. OpenAPI annotations are embedded in docblocks and collected through `swagger-php`.  
  - Feature directories such as `assets/`, `project/`, `maintenance/`, `login/`, `user/`, etc., pair PHP controllers with Twig templates. Widgets (e.g. `assets/widgets/statsWidgets.php`) are plain PHP classes consumed by Twig views.  
  - Templating: all UI is rendered with Twig (`*.twig`) using data assembled after including `headSecure.php`.
- **Configuration & Environment**  
  - Runtime configuration is stored in the `config` database table and managed by `Config` (`src/common/libs/Config/Config.php`); missing values render a Twig form at boot. `.env` primarily seeds database credentials and critical runtime flags consumed before the DB is available.  
  - Sentry DSN (`CONFIG_ERRORS_PROVIDERS_SENTRY`) and telemetry toggles live in config; Sentry scopes are populated once `$AUTH` is available.
- **Authentication & Permissions**  
  - `common/libs/Auth/main.php` instantiates `bID`, validates JWT/session tokens, hydrates `$AUTH->data`, and enforces both server-level (`serverPermissionCheck`) and instance-level (`instancePermissionCheck`) permissions. Session state is stored via PHP sessions plus the `authTokens` table.  
  - Instance context is set in session (`$_SESSION['instanceID']`) and mirrored on `$AUTH->data['instance']`.
- **Data Layer**  
  - Database access uses `MysqliDb` (third-party fluent wrapper). Helpers like `bCMS::sanitizeString()` and `bCMS::auditLog()` centralise sanitisation and logging.  
  - Schema lives in `db/migrations/20240101000000_core_schema.php`; MVP seeders reside in `db/seeds/`. Archived migrations remain in `legacy/db/migrations/`.
- **Legacy Surface (`legacy/`)**  
  - Contains modules cut from the MVP (business admin, CMS, CRM, training, expanded projects, server admin). Files mirror the original `src/` structure with lightweight shims back into the active code (e.g. `legacy/api/apiHead.php` requires `src/api/apiHead.php`). Use these as reference when restoring features or reviewing historical behaviour.
- **Documentation & Supporting Assets**  
  - `docs/INSTALLATION-RU.md` and `docs/mvp-module-manifest.md` capture installation steps and module scopes; `discription/` holds feature explainers.  
  - `Dockerfile` is multi-stage: Composer deps are built in a dedicated layer (`composer:lts`) and copied into the runtime image.

## Code Style & Conventions
- **Structure & Includes**  
  - Scripts start with `require_once` of shared bootstrap files; no PSR-4 autoloading beyond Composer packages. New shared classes should live in `src/common/libs/` and be `require_once`d where needed.  
  - Globals (`$DBLIB`, `$CONFIG`, `$CONFIGCLASS`, `$AUTH`, `$TWIG`, `$bCMS`) are populated by `head.php` and reused everywhere. Keep new globals to a minimum; prefer methods on existing helpers.
- **Naming & Formatting**  
  - Classes use `StudlyCaps` (`EmailHandler`, `Telemetry`). Methods and functions use `camelCase`. Database columns remain snake_case to match schema.  
  - Indentation is spaces (4 in most legacy files, 2 in some newer ones). Match the surrounding file. Brace style is K&R.  
  - Constants are uppercase (`TOKEN_LENGTH`). Magic strings for permissions/actions stay uppercase with colons (e.g. `"PROJECTS:VIEW"`).
- **API Endpoints**  
  - Always include `apiHeadSecure.php` for authenticated routes to ensure headers, payload hydration, and analytics logging. Handle permission gates early (`$AUTH->instancePermissionCheck`).  
  - Return data via `finish($result, $error, $response)`; avoid printing raw JSON manually. For error conditions use `finish(false, ['code' => '...','message' => '...'])`.  
  - When adding Swagger docs, follow the existing `@OA\*` annotations in `src/api/...`.
- **Database Access**  
  - Use `$DBLIB` chainable methods (`where`, `join`, `get`, `insert`, `update`) and sanitise free-form input with `$bCMS->sanitizeString()` or prepared statements.  
  - Track mutations in `auditLog` when relevant (`$bCMS->auditLog(...)`). Respect soft-delete patterns (`*_deleted` columns) and instance scoping (`instances_id`).  
  - Seeds should extend `Phinx\Seed\AbstractSeed` and declare dependencies via `getDependencies()`.
- **Templates & UI**  
  - Collect page data in `$PAGEDATA` before calling `$TWIG->render()`. Set `pageConfig` keys (`TITLE`, `BREADCRUMB`, etc.) for layout metadata.  
  - When injecting user/generated HTML, pass through `$bCMS->cleanString()` or Twig filters to avoid XSS.  
  - Reuse widgets (e.g. `statsWidgets`) rather than embedding heavy logic in templates.
- **Error Handling & Logging**  
  - Use exceptions sparingly (e.g. custom `AuthFail`) and convert to user-facing messages via `finish` or Twig views.  
  - Telemetry (`Telemetry::logTelemetry()`) and Sentry are opt-in based on config; wrap network calls in try/catch and surface warnings with `trigger_error`.  
  - Maintain analytics logging by inserting into `analyticsEvents`; both `headSecure.php` and `apiHeadSecure.php` already perform this—avoid duplicate inserts.

Keep AGENTS.md updated when introducing new tooling (linters, tests, build targets) or when module boundaries change.
