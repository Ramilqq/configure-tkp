<?php

use App\Livewire\Configuration\Configuration;
use App\Livewire\Configuration\NodeGroup;
use App\Livewire\Configuration\Setting;
use App\Livewire\Fr\FrList;
use App\Livewire\TableSettings\Product;
use App\Livewire\TableSettings\ProductList;
use App\Livewire\TableSettings\Template;
use App\Livewire\TableSettings\TemplateList;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');



//Route::get('table-settings/template-list', 'pages.template')->name('table-settings.template-list');



//Route::view('table-settings/template-list', 'components.pages.template')->name('table-settings.template-list');
//Route::view('table-settings/product-list/{template_id}', 'components.pages.product')->name('table-settings.product-list');

Route::get('table-settings/template-list}', Template::class)->name('table-settings.template-list');
Route::get('table-settings/product-list/{template_id}', Product::class)->name('table-settings.product-list');



Route::get('configuration}', Configuration::class)->name('configuration');
Route::get('configuration/setting}', NodeGroup::class)->name('configuration-node-group');


//Route::view('table-settings/product-list-test/{id}', 'components.pages.product')->name('table-settings.product-list-test');

