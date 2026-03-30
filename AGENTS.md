# Repository Guidelines

## Project Structure & Module Organization

- `app/` contains the Laravel API code: controllers, requests, resources, models, interfaces, repositories, helpers, and providers.
- `routes/api.php` defines API endpoints; most modules follow controller -> interface -> repository -> resource flow.
- `database/` holds migrations, factories, and seeders for domain data such as users, head of families, and family members.
- `tests/` uses Pest for feature and unit coverage; frontend assets live in `resources/` and are built with Vite.

## Build, Test, and Development Commands

- `composer run setup` installs dependencies, prepares `.env`, generates the app key, runs migrations, and builds assets.
- `php artisan test` or `composer test` runs the full backend test suite.
- `./vendor/bin/pint` formats PHP code to the project standard.
- `php artisan migrate:fresh --seed` resets the database and reseeds local data.
- Avoid starting the dev stack unless explicitly needed by the task.

## Coding Style & Naming Conventions

- Follow PSR-12 with 4-space indentation; use `laravel/pint` before finishing PHP changes.
- Match existing Laravel conventions: singular resource routes, `FormRequest` validation classes, `JsonResource` response shaping, and repository interfaces bound in `app/Providers/RepositoryServiceProvider.php`.
- Use existing naming patterns even where legacy quirks exist, such as `HeadofFamily*` alongside `HeadOfFamily`.
- Prefer eager loading in repositories when resources access nested relations directly.

## Testing Guidelines

- Pest is configured through `tests/Pest.php`; add tests under `tests/Feature` or `tests/Unit` with descriptive `*Test.php` filenames.
- Use focused runs while iterating, for example `php artisan test tests/Feature/ExampleTest.php`.
- Before wrapping up, run the relevant validators for your change at minimum: `./vendor/bin/pint --test` and `php artisan test`.

## Commit & Pull Request Guidelines

- Recent history uses Conventional Commit style such as `feat: implement family management API...`; keep messages concise and imperative.
- Before committing, review `git status`, `git diff --cached`, and check for secrets or accidental generated files.
- Pull requests should summarize scope, mention affected endpoints or tables, and include sample payloads when API responses change.

## Security & Configuration Tips

- Do not commit `.env`, secrets, or local build artifacts.
- File uploads use the `public` disk, so ensure `php artisan storage:link` is set up when working on upload features.
- Sanctum is installed, but API protection is only partially wired; verify auth assumptions before changing protected endpoints.
