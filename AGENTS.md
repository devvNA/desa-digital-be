# Desa Digital BE

## Project Snapshot

- Laravel 12 API on PHP 8.2 with Sanctum auth, Spatie permissions, Pest tests, and Vite + Tailwind for the default welcome page.
- API modules follow `controller -> interface -> repository -> resource`, with bindings in `app/Providers/RepositoryServiceProvider.php`.
- Domain areas include users, head of family, family members, social assistance, events, developments, dashboard metrics, global search, auth, and village profile.
- Read `TECHNICAL_OVERVIEW.md` for architecture and runtime details.
- Read `DESIGN_SYSTEM.md` for frontend and styling findings.

## Root Setup Commands

- Install and bootstrap: `composer run setup`
- Start local dev stack: `composer run dev`
- Run backend tests: `composer test`
- Run a focused test file: `php artisan test tests/Feature/ExampleTest.php`
- Check PHP formatting: `./vendor/bin/pint --test`
- Apply PHP formatting: `./vendor/bin/pint`
- Reset and seed database: `php artisan migrate:fresh --seed`
- Build frontend assets: `npm run build`

## Universal Conventions

- Follow PSR-12 with 4-space indentation.
- Preserve the existing Laravel structure in `app/`, `routes/`, `database/`, and `tests/`.
- Prefer `FormRequest` validation, `JsonResource` response shaping, and repository interface injection.
- Keep naming aligned with existing code, including legacy spellings such as `HeadofFamily*` and `PaginateResourse`.
- Prefer eager loading in repositories when resources serialize nested relations.
- Keep API responses consistent with `App\Helpers\ResponseHelper` or the module's established JSON shape.
- Do not duplicate architecture notes already documented in `TECHNICAL_OVERVIEW.md`.
- Do not duplicate styling notes already documented in `DESIGN_SYSTEM.md`.

## Security & Secrets

- Never commit `.env`, credentials, tokens, or local build artifacts.
- File uploads use the `public` disk under `storage/app/public/assets/...`; account for `php artisan storage:link` when relevant.
- Protected API routes are grouped under `auth:sanctum` in `routes/api.php`; verify auth and permission assumptions before changing access behavior.

## JIT Index

### Key Docs

- Architecture overview: `TECHNICAL_OVERVIEW.md`
- Frontend patterns: `DESIGN_SYSTEM.md`
- Project initialization brief: `INITIALIZE PROJECT.md`

### Package Structure

- API controllers: `app/Http/Controllers/`
- Request validators: `app/Http/Requests/`
- JSON resources: `app/Http/Resources/`
- Repository contracts: `app/Interfaces/`
- Repository implementations: `app/Repositories/`
- Eloquent models: `app/Models/`
- Service bindings: `app/Providers/RepositoryServiceProvider.php`
- API routes: `routes/api.php`
- Migrations, factories, seeders: `database/`
- Tests: `tests/`

### Quick Find Commands

- Find a controller or repository: `rg -n "class .*Controller|class .*Repository" app`
- Find a specific route: `rg -n "Route::(get|post|put|delete|apiResource)" routes/api.php`
- Find interface bindings: `rg -n "bind\(" app/Providers/RepositoryServiceProvider.php`
- Find request validators: `rg -n "extends FormRequest" app/Http/Requests`
- Find resource serializers: `rg -n "extends JsonResource" app/Http/Resources`
- Find upload paths: `rg -n -- "->store\('assets/" app/Repositories`
- Find eager loads: `rg -n "with\(|load\(" app/Repositories`
- Find permission middleware: `rg -n "PermissionMiddleware::using|implements HasMiddleware" app/Http/Controllers`

## Working Rules For Agents

- Read the target controller, repository, request, resource, and related model before editing an API feature.
- Keep diffs scoped to the agreed files and avoid opportunistic refactors.
- Reuse existing response envelopes, transaction handling, and relation-loading patterns.
- When changing database behavior, inspect the related migration, model, and seeder first.
- When changing frontend assets, inspect `DESIGN_SYSTEM.md`, `resources/css/app.css`, and the relevant Blade file before editing.

## Definition of Done

- Show objective proof with the exact validation commands run for the changed area.
- Minimum PHP proof for backend changes: `./vendor/bin/pint --test` and `php artisan test` or a clearly scoped `php artisan test <path>`.
- Minimum frontend proof for asset/view changes: `npm run build`.
- Review the final diff scoped to the edited paths with `git diff -- <path1> <path2>`.
- Do not mark work complete if tests, lint, or build fail without explicitly stating the failure.
