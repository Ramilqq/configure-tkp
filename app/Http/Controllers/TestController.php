<?php

namespace App\Http\Controllers;

use App\Models\TableSettings\Product;
use App\Models\TableSettings\ProductOption;
use App\Models\TableSettings\ProductOptionPrice;
use App\Models\TableSettings\TemplateOption;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index() {

        $message = "Constructor bug\r\n\nТекст ошибки...";

        $headers = [
            'From' => 'tkp_bot@ru-drive.com',
            'Reply-To' => 'tkp_bot@ru-drive.com',
            'X-Mailer' => 'PHP/' . phpversion(),
            'Content-Type' => 'text/plain; charset=UTF-8',
        ];

        $ok = \mail(
            'hadievrf@ru-drive.com',
            'Test',
            $message,
            $headers,
            '-ftkp_bot@ru-drive.com'
        );

        dd($ok, error_get_last());
    }
}
