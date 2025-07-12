<?php

use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\UsersController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/dashboard');
    /* return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]); */
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::get('/users/list', [UsersController::class, 'list'])->name('users.list');
    Route::post('/users', [UsersController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UsersController::class, 'show'])->name('users.show');
    Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');

    Route::get('/documents', [DocumentsController::class, 'index'])->name('documents.index');
    Route::get('/documents/{document}', [DocumentsController::class, 'show'])->name('documents.show');
    Route::put('/documents/{document}', [DocumentsController::class, 'update'])->name('documents.update');
    Route::get('/documents/{document}/download', [DocumentsController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [DocumentsController::class, 'destroy'])->name('documents.destroy');

    Route::post('/upload', [FileUploadController::class, 'upload'])->middleware(['role_or_permission:upload documents', 'throttle:10,1'])->name('upload');

    Route::get('/companies', [CompaniesController::class, 'index'])->name('companies.index');
    Route::get('/companies-list', [CompaniesController::class, 'getUserCompanies'])->name('companies.list');
    Route::get('/companies/{company}', [CompaniesController::class, 'show'])->name('companies.show');
    Route::post('/companies', [CompaniesController::class, 'store'])->name('companies.store');
    Route::put('/companies/{company}', [CompaniesController::class, 'update'])->name('companies.update');
    Route::delete('/companies/{company}', [CompaniesController::class, 'destroy'])->name('companies.destroy');
    Route::delete('/companies/{company}/logo', [CompaniesController::class, 'destroyLogo'])->name('companies.destroyLogo');
});
