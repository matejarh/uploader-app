<p align="center">
  <img src="resources/images/logo.svg" width="260" alt="Uploader App Logo" />
</p>

<p align="center">
  <img alt="CI Status" src="https://img.shields.io/github/actions/workflow/status/matejarh/uploader-app/tests.yml?branch=main&label=CI" />
  <img alt="PHP Version" src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white" />
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel&logoColor=white" />
  <img alt="License" src="https://img.shields.io/badge/license-MIT-green" />
</p>

## About Uploader App

Uploader App is a Laravel application for uploading, organizing, and managing company documents. It includes user authentication, permissions, company management, document tracking, and a document archive flow.

## Features

- User registration and login
- Role-based access control with Spatie permissions
- Company-aware document upload workflow
- Document archive support for inactive records
- User and company management
- Two-factor authentication
- Dark mode support

## Requirements

- PHP 8.3+
- Composer
- Node.js + npm
- MySQL or another supported Laravel database

## Installation

1. Clone the repository:
    ```sh
    git clone https://github.com/yourusername/uploader-app.git
    cd uploader-app
    ```

2. Install PHP and frontend dependencies:
    ```sh
    composer install
    npm install
    ```

3. Copy the environment file and configure the database/mail settings:
    ```sh
    cp .env.example .env
    ```

4. Generate the application key:
    ```sh
    php artisan key:generate
    ```

5. Run the database migrations and seeders:
    ```sh
    php artisan migrate --seed
    ```

6. Start the development build and app:
    ```sh
    npm run dev
    php artisan serve
    ```

## Registration and roles

New users are created through Fortify registration. The default `client` role is assigned only when that role actually exists in the database.

This prevents registration from crashing on a fresh install before permissions are seeded, while still assigning the correct role in the normal seeded app flow.

## Usage

1. Register a new user or log in with an existing account.
2. Upload documents from the dashboard or documents screen.
3. Use the archive toggle to hide documents from the active list without deleting them.
4. Manage users and roles in the admin area.
5. Enable two-factor authentication for added security.

## Running Tests

To run the full suite:
```sh
php artisan test
```

For the most relevant checks around registration and uploads:
```sh
php artisan test tests/Feature/RegistrationTest.php tests/Feature/ProfileInformationTest.php tests/Feature/FileUploadTest.php
```
