# Student Attendance & At-Risk Monitoring System

A comprehensive web application designed for educators to easily import, track, and monitor student attendance across multiple semesters and class groups. The system automatically highlights at-risk students who fall below a certain attendance threshold, allowing for early intervention.

## Core Features

- **Multi-Tenant Architecture**: Each registered user gets their own private workspace for their semesters and groups.
- **Hierarchical Management**: Organize attendance by `Semester -> Group`.
- **Automated PDF Parsing**: Upload raw attendance PDF exports directly into a group. The system automatically parses matrix numbers, names, and attendance statuses.
- **Master Attendance Grid**: A consolidated, spreadsheet-like view showing a student's total attendance rate, status, and week-by-week presence.
- **At-Risk Detection**: Students falling below the 80% attendance rate threshold are automatically flagged in red.
- **Smart Filtering & Sorting**: Quickly filter the grid to show only "At-Risk" or "Good Standing" students, and sort by student name.
- **Custom Email Verification**: Secure registration flow utilizing a custom 6-digit email verification code rather than standard magic links.

## Tech Stack

This project was rapidly developed using a modern PHP stack:

- **Framework**: [Laravel 11](https://laravel.com/)
- **Authentication**: Laravel Breeze (Blade stack)
- **Frontend / Styling**: [Tailwind CSS](https://tailwindcss.com/) & Vanilla JavaScript
- **Database**: MySQL (via Eloquent ORM)
- **PDF Extraction**: [Smalot/pdfparser](https://github.com/smalot/pdfparser) for robust backend text extraction from uploaded documents.

## Local Setup & Development

1. Clone the repository.
2. Run `composer install` and `npm install`.
3. Copy `.env.example` to `.env` and configure your database settings.
4. Set `MAIL_MAILER=log` in your `.env` to test registration codes locally (they will print to `storage/logs/laravel.log`).
5. Run `php artisan key:generate`.
6. Run `php artisan migrate --seed` to scaffold the database.
7. Run `npm run build` to compile the Tailwind CSS assets.
8. Serve the application via `php artisan serve` or Docker (`docker compose up -d --build`).

## Running with Docker

This project uses [dockerfile](dockerfile) (`php:8.5-apache` + `pdo_mysql`, `zip`, `gd`, Composer, Node.js 22) and [compose.yaml](compose.yaml) (`app` + `mysql:8.4`).

### 1. Prerequisites
- Install and start **Docker Desktop** (with WSL 2 enabled on Windows).
- Stop Laragon (or any local Apache/Nginx/MySQL) so ports `80` and `3306` do not conflict. *(Alternatively, set `APP_PORT=8080` and `FORWARD_DB_PORT=3307` in `.env`.)*

### 2. Configure `.env`
Update your `.env` file to point to the `mysql` Docker service:

```env
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=lab_demo
DB_USERNAME=root
DB_PASSWORD=
```

*(If you set a `DB_PASSWORD`, MySQL will use it as `MYSQL_ROOT_PASSWORD`; if left empty, `MYSQL_ALLOW_EMPTY_PASSWORD=1` allows passwordless root access locally.)*

### 3. Build & Start the Containers
From the project root, build the image and start the services in the background:

```powershell
docker compose up -d --build
```

### 4. Initial Setup & Migrations
Run Composer, Artisan, and Vite commands inside the `app` container:

```powershell
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app npm install
docker compose exec app npm run build
```

### 5. (Optional) Import Existing Database Backup
If migrating from Laragon, import your `.sql` dump (e.g. `lab_demo.sql`) into the Docker MySQL container *(omit `-p` if `DB_PASSWORD` is empty)*:

```powershell
Get-Content lab_demo.sql -Raw | docker compose exec -T mysql mysql -uroot -p
```

To verify the imported tables and records:

```powershell
docker compose exec mysql mysql -uroot -p -e "SHOW TABLES; SELECT COUNT(*) FROM students;" lab_demo
```

### 6. Access & Stop
- **Open the app:** `http://localhost` (or `http://localhost:8080` if `APP_PORT` was changed).
- **Stop containers:** `docker compose down` *(database data persists in the `mysql-data` volume)*.
