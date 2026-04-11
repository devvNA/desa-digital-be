# Technical Overview

## Core Components

### Tech Stack

| Area              | Implementation                                                                                                                                                                   |
| ----------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Backend framework | Laravel 12 on PHP 8.2 (`composer.json`)                                                                                                                                          |
| Authentication    | Laravel Sanctum with a custom UUID-based personal access token model (`app/Models/PersonalAccessToken.php`, `app/Providers/AppServiceProvider.php`)                              |
| Authorization     | Spatie Laravel Permission with permission aliases and seeded roles/permissions (`bootstrap/app.php`, `database/seeders/PermissionSeeder.php`, `database/seeders/RoleSeeder.php`) |
| Persistence       | Eloquent models with UUID primary keys and mostly soft deletes across domain tables (`app/Models/*.php`, `database/migrations/*.php`)                                            |
| API shape         | REST-style JSON API under `routes/api.php`, backed by controllers, request validators, repositories, and JSON resources                                                          |
| Frontend assets   | Vite + Tailwind CSS v4 for the default welcome page only (`vite.config.js`, `resources/css/app.css`, `resources/views/welcome.blade.php`)                                        |
| Testing           | Pest on top of Laravel's test case (`tests/Pest.php`)                                                                                                                            |

### Major Modules

| Module                       | Main entrypoints                                                                                                                                                       | Responsibility                                                                                    |
| ---------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------- |
| Auth                         | `app/Http/Controllers/AuthController.php`, `app/Repositories/AuthRepository.php`                                                                                       | Login, logout, current-user lookup, Sanctum token issuance and deletion                           |
| Dashboard                    | `app/Http/Controllers/DashboardController.php`, `app/Repositories/DashboardRepository.php`                                                                             | Aggregate counts for residents, head of families, assistance recipients, events, and developments |
| Users                        | `app/Http/Controllers/UserController.php`, `app/Repositories/UserRepository.php`, `app/Models/User.php`                                                                | CRUD for application users, password hashing, role-enabled identity model                         |
| Head of family               | `app/Http/Controllers/HeadofFamilyController.php`, `app/Repositories/HeadofFamilyRepository.php`, `app/Models/HeadOfFamily.php`                                        | CRUD for household heads plus linked `users` records and profile-photo upload                     |
| Family members               | `app/Http/Controllers/FamilyMemberController.php`, `app/Repositories/FamilyMemberRepository.php`, `app/Models/FamilyMember.php`                                        | CRUD for household members linked to a head of family and a `users` row                           |
| Social assistance            | `app/Http/Controllers/SocialAssistanceController.php`, `app/Repositories/SocialAssistanceRepository.php`, `app/Models/SocialAssistance.php`                            | CRUD for assistance programs with thumbnails and availability flag                                |
| Social assistance recipients | `app/Http/Controllers/SocialAssistanceRecipientController.php`, `app/Repositories/SocialAssistanceRecipientRepository.php`, `app/Models/SocialAssistanceRecipient.php` | Track recipient applications/awards, proof uploads, bank/account metadata, and status             |
| Events                       | `app/Http/Controllers/EventController.php`, `app/Repositories/EventRepository.php`, `app/Models/Event.php`                                                             | CRUD for event records with pricing, schedule, activity flag, and thumbnail upload                |
| Event participants           | `app/Http/Controllers/EventParticipantController.php`, `app/Repositories/EventParticipantRepository.php`, `app/Models/EventParticipant.php`                            | Register heads of family into events and derive `total_price` from event price and quantity       |
| Developments                 | `app/Http/Controllers/DevelopmentController.php`, `app/Repositories/DevelopmentRepository.php`, `app/Models/Development.php`                                           | CRUD for village development programs with status, date range, and funding amount                 |
| Development applicants       | `app/Http/Controllers/DevelopmentApplicantController.php`, `app/Repositories/DevelopmentApplicantRepository.php`, `app/Models/DevelopmentApplicant.php`                | Link users to development programs with optional status                                           |
| Village profile              | `app/Http/Controllers/ProfileController.php`, `app/Repositories/ProfileRepository.php`, `app/Models/Profile.php`, `app/Models/ProfileImage.php`                        | Store a single village profile record, thumbnail, gallery images, and summary statistics          |

### Design Patterns and Conventions

| Pattern                                     | Evidence                                                                                                                                                                                                                                 |
| ------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Controller -> repository -> resource flow   | `routes/api.php` points to controllers; controllers inject repository interfaces from `app/Interfaces`; repositories are bound in `app/Providers/RepositoryServiceProvider.php`; responses are transformed by `app/Http/Resources/*.php` |
| Request validation objects                  | Create/update endpoints use `FormRequest` classes such as `app/Http/Requests/HeadofFamilyStoreRequest.php` and `app/Http/Requests/ProfileUpdateRequest.php`                                                                              |
| Dependency injection via container bindings | Interface-to-concrete bindings are centralized in `app/Providers/RepositoryServiceProvider.php` and loaded from `bootstrap/providers.php`                                                                                                |
| Shared JSON envelope helper                 | `app/Helpers/ResponseHelper.php` standardizes `{ success, message, data }` and common error payloads                                                                                                                                     |
| Transactional write operations              | Repositories wrap create/update/delete flows in `DB::beginTransaction()` / `commit()` / `rollBack()`                                                                                                                                     |
| Eager loading for resource graphs           | Repositories load nested relations with `with(...)` or `load(...)` before resource serialization, e.g. `HeadofFamilyRepository`, `FamilyMemberRepository`, `SocialAssistanceRepository`, `ProfileRepository`                             |

## Component Interactions

### Request and Response Flow

1. An API request enters through `routes/api.php`.
2. Most routes inside the authenticated group pass through `auth:sanctum`; several CRUD controllers also attach Spatie permission middleware by implementing `HasMiddleware`.
3. Controller actions validate input through `FormRequest` classes or inline `$request->validate(...)`.
4. Controllers delegate business logic and persistence to repository interfaces.
5. Repositories read and write Eloquent models, often with eager-loaded relations and database transactions.
6. Controllers wrap returned models or collections in `JsonResource` classes and emit JSON via `ResponseHelper` or inline `response()->json(...)`.

### Service and DI Structure

| Interface                                      | Concrete class                        |
| ---------------------------------------------- | ------------------------------------- |
| `AuthRepositoryInterface`                      | `AuthRepository`                      |
| `DashboardRepositoryInterface`                 | `DashboardRepository`                 |
| `UserRepositoryInterface`                      | `UserRepository`                      |
| `HeadOfFamilyRepositoryInterface`              | `HeadofFamilyRepository`              |
| `FamilyMemberRepositoryInterface`              | `FamilyMemberRepository`              |
| `SocialAssistanceRepositoryInterface`          | `SocialAssistanceRepository`          |
| `SocialAssistanceRecipientRepositoryInterface` | `SocialAssistanceRecipientRepository` |
| `EventRepositoryInterface`                     | `EventRepository`                     |
| `EventParticipantRepositoryInterface`          | `EventParticipantRepository`          |
| `DevelopmentRepositoryInterface`               | `DevelopmentRepository`               |
| `DevelopmentApplicantRepositoryInterface`      | `DevelopmentApplicantRepository`      |
| `ProfileRepositoryInterface`                   | `ProfileRepository`                   |

### Domain Relationships

| Relationship                                                       | Evidence                                                                                      |
| ------------------------------------------------------------------ | --------------------------------------------------------------------------------------------- |
| A `User` has one `HeadOfFamily`                                    | `app/Models/User.php`, `app/Models/HeadOfFamily.php`                                          |
| A `HeadOfFamily` has many `FamilyMember` records                   | `app/Models/HeadOfFamily.php`, `app/Models/FamilyMember.php`                                  |
| A `SocialAssistance` has many recipient records                    | `app/Repositories/SocialAssistanceRepository.php`, `app/Models/SocialAssistanceRecipient.php` |
| A `Development` has many applicants                                | `app/Repositories/DevelopmentRepository.php`, `app/Models/DevelopmentApplicant.php`           |
| An `EventParticipant` derives total price from its related `Event` | `app/Repositories/EventParticipantRepository.php`                                             |
| A `Profile` has many `ProfileImage` records                        | `app/Repositories/ProfileRepository.php`, `app/Http/Resources/ProfileResource.php`            |

### API Surface

Authenticated routes in `routes/api.php` expose `dashboard`, `user`, `head-of-family`, `family-member`, `social-assistance`, `social-assistance-recipient`, `event`, `event-participant`, `development`, `development-applicant`, `profile`, `logout`, and `me`.

Public routes in `routes/api.php` expose `login` and `register`. `AuthController` implements `login`, `logout`, and `me`; no `register` method is defined in `app/Http/Controllers/AuthController.php`.

## Deployment Architecture

### Build and Setup

| Task                  | Command / implementation                                                                                                                  |
| --------------------- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| Initial project setup | `composer run setup` runs Composer install, `.env` bootstrap, key generation, migrations, `npm install`, and Vite build (`composer.json`) |
| Development mode      | `composer run dev` starts `php artisan serve`, `php artisan queue:listen --tries=1`, and `npm run dev` concurrently (`composer.json`)     |
| Asset build           | `npm run build` via Vite (`package.json`, `vite.config.js`)                                                                               |
| Test run              | `composer test` clears config then runs `php artisan test` (`composer.json`)                                                              |

### Environment and Infrastructure Signals

| Topic                  | Evidence                                                                                                                                                                                                    |
| ---------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Environment file       | `.env` is expected by Composer scripts and is copied from `.env.example` when absent (`composer.json`)                                                                                                      |
| Storage-backed uploads | Repositories store files on the `public` disk under `assets/...` paths                                                                                                                                      |
| Queue infrastructure   | Default jobs migration exists (`database/migrations/0001_01_01_000002_create_jobs_table.php`) and the dev script starts `queue:listen`, but no application-specific queued job classes were found in `app/` |
| Containers             | `laravel/sail` is present in `require-dev`, but no Docker or Sail config files were found in the project root                                                                                               |

## Runtime Behavior

### Application Initialization

`bootstrap/app.php` configures web, API, console, and health routes; aliases Spatie role and permission middleware; and registers JSON renderers for validation failures and authorization failures on API requests.

`bootstrap/providers.php` registers `AppServiceProvider`, `RepositoryServiceProvider`, and Spatie's `PermissionServiceProvider`.

`AppServiceProvider` points Sanctum to the custom `App\Models\PersonalAccessToken` model so token IDs use UUID strings.

### Auth and Authorization Runtime

`AuthRepository` authenticates credentials with `Auth::attempt`, creates a Sanctum token named `auth_token`, deletes the current access token on logout, and returns the authenticated user's first role plus flattened permission names on `/me`.

Permission data is seeded from `PermissionSeeder` and `RoleSeeder`; the `admin` role receives all permissions, while `head-of-family` receives a limited subset centered on dashboard, family, assistance recipient, event participant, development applicant, and profile access.

### Error Handling

| Error path                       | Behavior                                                                                                                             |
| -------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------ |
| Validation failures              | `bootstrap/app.php` returns `{ success: false, message: 'Validasi gagal.', errors, data: null }` with HTTP 422 for API/JSON requests |
| Permission failures              | `bootstrap/app.php` returns `{ success: false, message: 'Akses ditolak.', errors.authorization[...] }` with HTTP 403                 |
| Repository/controller exceptions | Most controllers catch exceptions and return 500 JSON responses, usually through `ResponseHelper`                                    |
| Duplicate head-of-family keys    | `HeadofFamilyController` maps duplicate key errors for `identity_number`, `phone_number`, and `email` to a 409 response              |

### Background and Scheduled Work

No scheduled tasks, job dispatches, or queueable application classes were found in `app/`. Runtime queue activity is limited to the development command defined in `composer.json`.
