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
8. Serve the application via `php artisan serve` or Laravel Sail (`./vendor/bin/sail up`).
