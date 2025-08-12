<?php

namespace App\Providers;

use App\Repositories\Interfaces\MessageQueueRepositoryInterface;
use App\Services\CacheService;
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
            return new MessageService(
                $app->make(MessageQueueRepositoryInterface::class),
                $app->make(WebhookService::class),
                $app->make(CacheService::class)
            );
        });

        $this->app->singleton(WebhookService::class, function ($app) {
            return new WebhookService();
        });

        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService();
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
