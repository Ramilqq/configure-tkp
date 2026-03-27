<?php

use App\Http\Controllers\PdfController;
use App\Livewire\Configuration\NodeGroup;
use App\Livewire\TableSettings\Product;
use App\Livewire\TableSettings\Template;
use App\Livewire\Tkp\TkpCalculation;
use App\Livewire\Tkp\TkpConfiguration;
use App\Livewire\Tkp\TkpContact;
use App\Livewire\Tkp\TkpDelivery;
use App\Livewire\Tkp\TkpList;
use Illuminate\Support\Facades\Route;

Route::get('/test', [App\Http\Controllers\TestController::class, 'index'])->name('test');

// если гость или не авторизован
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'index'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'store'])->name('login.store');

    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'index'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'store'])->name('register.store');

    // Сброс пароля
    Route::get('/forgot-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'sendLink'])->name('password.email');
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('password.update');
});

// подтверждение почты
Route::get('/email/verify', [App\Http\Controllers\Auth\EmailVerificationController::class, 'notice'])
    //->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\EmailVerificationController::class, 'verify'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [App\Http\Controllers\Auth\EmailVerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// Выход
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// работа пользователя 
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', TkpList::class)->name('home');

    Route::get('table-settings/template-list', Template::class)->name('table-settings.template-list');
    Route::get('table-settings/product-list/{template_id}', Product::class)->name('table-settings.product-list');

    Route::get('configuration/setting', NodeGroup::class)->name('configuration-node-group');

    Route::get('tkp/contact', TkpContact::class)->name('tkp.contact');
    Route::get('tkp/contact/{id}/{tkp_version}', TkpContact::class)->name('tkp.contact.edit');
    Route::get('tkp/sheme/{id}/{tkp_version}', TkpConfiguration::class)->name('tkp.sheme.edit');
    Route::get('tkp/delivery/{id}/{tkp_version}', TkpDelivery::class)->name('tkp.delivery.edit');
    Route::get('tkp/calculation/{id}/{tkp_version}', TkpCalculation::class)->name('tkp.calculation.edit');
    Route::get('/tkp/pdf/{id}/{tkp_version}',    [PdfController::class, 'show'])->name('tkp.pdf.show'); 

    Route::get('/pdf-preview', [PdfController::class, 'preview']);

    Route::get('table-settings/products/excel-import', App\Livewire\TableSettings\ProductExcelImport::class)->name('table-settings.products.excel-import');

    Route::get('table-settings/dimension-schemes/{template_id}', App\Livewire\TableSettings\TemplateDimensionScheme::class)->name('table-settings.dimension-schemes');

    Route::get('tkp/engineering-list', App\Livewire\Tkp\EngineeringList::class)->name('tkp.engineering-list');
    Route::get('tkp/manufacturer-list', App\Livewire\Tkp\ManufacturerList::class)->name('tkp.manufacturer-list');
});
