<?php

use App\Http\Controllers\ExportArrayController;
use App\Http\Controllers\PdfController;
use App\Livewire\Configuration\Configuration;
use App\Livewire\Configuration\NodeGroup;
use App\Livewire\Configuration\Setting;
use App\Livewire\Fr\FrList;
use App\Livewire\TableSettings\Product;
use App\Livewire\TableSettings\ProductList;
use App\Livewire\TableSettings\Template;
use App\Livewire\TableSettings\TemplateList;
use App\Livewire\Tkp\TkpCalculation;
use App\Livewire\Tkp\TkpConfiguration;
use App\Livewire\Tkp\TkpContact;
use App\Livewire\Tkp\TkpDelivery;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');



//Route::get('table-settings/template-list', 'pages.template')->name('table-settings.template-list');



//Route::view('table-settings/template-list', 'components.pages.template')->name('table-settings.template-list');
//Route::view('table-settings/product-list/{template_id}', 'components.pages.product')->name('table-settings.product-list');

Route::get('table-settings/template-list', Template::class)->name('table-settings.template-list');
Route::get('table-settings/product-list/{template_id}', Product::class)->name('table-settings.product-list');



Route::get('configuration', Configuration::class)->name('configuration');
Route::get('configuration/setting', NodeGroup::class)->name('configuration-node-group');

Route::get('tkp/contact', TkpContact::class)->name('tkp.contact');
Route::get('tkp/contact/{id}/{tkp_version}', TkpContact::class)->name('tkp.contact.edit');
Route::get('tkp/sheme/{id}/{tkp_version}', TkpConfiguration::class)->name('tkp.sheme.edit');
Route::get('tkp/delivery/{id}/{tkp_version}', TkpDelivery::class)->name('tkp.delivery.edit');
Route::get('tkp/calculation/{id}/{tkp_version}', TkpCalculation::class)->name('tkp.calculation.edit');
Route::get('/tkp/pdf/{id}/{tkp_version}',    [PdfController::class, 'show'])->name('tkp.pdf.show'); 

//Route::get('tkp/{id}/pdf', [PdfController::class, 'show'])->name('tkp.pdf');

Route::get('/pdf-preview', [PdfController::class, 'preview']);
Route::get('/export/array', [ExportArrayController::class, 'export']);
//Route::view('table-settings/product-list-test/{id}', 'components.pages.product')->name('table-settings.product-list-test');

