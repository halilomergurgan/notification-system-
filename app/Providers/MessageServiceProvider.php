<?php

namespace App\Providers;

use App\Services\MessageService;
use App\Services\WebhookService;
use Illuminate\Support\ServiceProvider;

class MessageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(MessageService::class, function ($app) {
            return new MessageService();
        });

        $this->app->singleton(WebhookService::class, function ($app) {
            return new WebhookService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
