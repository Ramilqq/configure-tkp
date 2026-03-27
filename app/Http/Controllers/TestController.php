<?php

namespace App\Http\Controllers;

use App\Models\TableSettings\ProductOption;
use App\Models\TableSettings\ProductOptionPrice;
use App\Models\TableSettings\TemplateOption;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index() {

        $template_options = TemplateOption::all();
        $product_options = ProductOption::all();
        $product_option_prices = ProductOptionPrice::all();

        dd(
            'template_options=' . count($template_options),
            'product_options=' . count($product_options),
            'product_option_prices=' . count($product_option_prices),
        );
    }
}
