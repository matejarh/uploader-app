<p align="center"><a href="https://example.com" target="_blank"><img src="https://example.com/logo.svg" width="400" alt="Uploader App Logo"></a></p>

<p align="center">
<a href="https://github.com/example/uploader-app/actions"><img src="https://github.com/example/uploader-app/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/example/uploader-app"><img src="https://img.shields.io/packagist/dt/example/uploader-app" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/example/uploader-app"><img src="https://img.shields.io/packagist/v/example/uploader-app" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/example/uploader-app"><img src="https://img.shields.io/packagist/l/example/uploader-app" alt="License"></a>
</p>

## About Uploader App

Uploader App is a web application built with Laravel that allows users to upload, manage, and view documents. The application supports user roles and permissions, ensuring that only authorized users can perform certain actions.

## Features

- User authentication and authorization
- Role-based access control
- Document upload and management
- View and download documents
- Two-factor authentication
- Dark mode support

## Installation

1. Clone the repository:
    ```sh
    git clone https://github.com/yourusername/uploader-app.git
    cd uploader-app
    ```

2. Install dependencies:
    ```sh
    composer install
    npm install
    npm run dev
    ```

3. Copy the [.env.example](http://_vscodecontentref_/1) file to [.env](http://_vscodecontentref_/2) and configure your environment variables:
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

6. Start the development server:
    ```sh
    php artisan serve
    ```

## Usage

1. Register a new user or log in with an existing account.
2. Upload documents by navigating to the "Documents" section.
3. Manage users and roles in the "Users" section (admin only).
4. Enable two-factor authentication for added security.


## Running Tests

To run the tests, use the following command:
```sh
php artisan test
```
