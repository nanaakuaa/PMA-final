# PMA (m-vault)

Secure password and secret management application built with Laravel.

## Overview

PMA (also referenced as m-vault) is a self-hosted password management backend that stores, shares, and audits secrets. The codebase includes models for `Password`, `Folder`, `PasswordShare`, `TwoFactorAuthentication`, `UserSession`, and services such as `EncryptionService`, `PasswordGeneratorService`, and `TwoFactorService`.

Key features
- Store encrypted passwords and metadata
- Share passwords with other users via `PasswordShare`
- Folder organization and access policies
- Audit logging for important events
- Two-factor authentication support
- Push/email notifications for password actions

## Requirements
- PHP 8.1+ (or the version required by your Laravel release)
- Composer
- Node.js 18+ and npm/yarn (for frontend assets via Vite)
- A supported database (MySQL, PostgreSQL, SQLite)

## Quick setup
1. Clone the repository and enter the project folder

```bash
git clone <repo-url> pma
cd pma
```

2. Install PHP dependencies

```bash
composer install --no-interaction --prefer-dist
```

3. Install frontend dependencies and build assets

```bash
npm install
# local dev
npm run dev
# or build for production
npm run build
```

4. Environment

Copy `.env.example` to `.env` and update values (database, mail, app URL, push credentials).

```bash
cp .env.example .env
php artisan key:generate
```

5. Database

Run migrations and seeders (if provided).

```bash
php artisan migrate --seed
```

6. Storage and permissions

Create the storage symlink and ensure `storage/` is writable:

```bash
php artisan storage:link
```

7. Run the app

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

## Configuration notes
- `APP_URL`: base URL for links and notifications
- `DB_CONNECTION`, `DB_DATABASE`, etc.: database settings
- Mail and push notification settings: configure in `.env` (see `config/mail.php` and `config/services.php`)
- Encryption keys: application key via `php artisan key:generate`; the project may use custom `EncryptionService` for per-record encryption.

## Important artisan commands
- `php artisan migrate` — run database migrations
- `php artisan db:seed` — run seeders
- `php artisan test` or `vendor/bin/phpunit` — run tests
- `php artisan tinker` — interact with the application

## Testing

Run the test suite with:

```bash
php artisan test
# or
vendor/bin/phpunit
```

## Development pointers
- Models live in `app/Models`
- Policies live in `app/Policies` (authorization rules for folders, passwords, audit logs)
- Domain services live in `app/Services` (`EncryptionService`, `PasswordGeneratorService`, `TwoFactorService`)
- Events & listeners for password actions in `app/Events` and `app/Listeners`
- Notifications in `app/Notifications`

## Deployment
- Use a process manager (Supervisor, systemd) for queue workers and scheduled tasks
- Configure a webserver (Nginx/Apache) to serve `public/` and point `APP_ENV=production`
- Run `npm run build` to compile production assets and `php artisan migrate --force` for migrations

## Contributing
- Fork the repo, create a feature branch, and open a pull request
- Follow PSR coding standards and run tests locally before PR

## License
This project is licensed under the MIT License unless otherwise stated.

---

If you'd like, I can also:
- Add/clean up badges and a project description
- Add setup scripts (Makefile / composer scripts)
- Add a CONTRIBUTING.md

Tell me which of those you'd like next.
