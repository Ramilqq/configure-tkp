<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Tkp\Tkp::class => \App\Policies\TkpPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('path.public', function () {
            return base_path('public_html');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Регистрация policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Подхватываем схему из APP_URL и заставляем URL генератор быть последовательным
        $appUrl = config('app.url'); // берётся из APP_URL
        if ($appUrl) {
            $scheme = parse_url($appUrl, PHP_URL_SCHEME);
            if ($scheme) {
                URL::forceScheme($scheme);
            }
            URL::forceRootUrl($appUrl);
        }
    }
}
