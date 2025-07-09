<?php

use App\Livewire\Fr\FrList;
use App\Livewire\TableSettings\Product;
use App\Livewire\TableSettings\ProductList;
use App\Livewire\TableSettings\Template;
use App\Livewire\TableSettings\TemplateList;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



//Route::get('table-settings/template-list', 'pages.template')->name('table-settings.template-list');



//Route::view('table-settings/template-list', 'components.pages.template')->name('table-settings.template-list');
//Route::view('table-settings/product-list/{template_id}', 'components.pages.product')->name('table-settings.product-list');

Route::get('table-settings/template-list}', Template::class)->name('table-settings.template-list');
Route::get('table-settings/product-list/{template_id}', Product::class)->name('table-settings.product-list');


//Route::view('table-settings/product-list-test/{id}', 'components.pages.product')->name('table-settings.product-list-test');

