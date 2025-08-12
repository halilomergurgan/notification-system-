<?php

namespace App\Providers;

use App\Repositories\Eloquent\MessageQueueRepository;
use App\Repositories\Eloquent\MessageRepository;
use App\Repositories\Eloquent\MessageTemplateRepository;
use App\Repositories\Eloquent\RecipientRepository;
use App\Repositories\Interfaces\MessageQueueRepositoryInterface;
use App\Repositories\Interfaces\MessageRepositoryInterface;
use App\Repositories\Interfaces\MessageTemplateRepositoryInterface;
use App\Repositories\Interfaces\RecipientRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
        $this->app->bind(RecipientRepositoryInterface::class, RecipientRepository::class);
        $this->app->bind(MessageQueueRepositoryInterface::class, MessageQueueRepository::class);
        $this->app->bind(MessageTemplateRepositoryInterface::class, MessageTemplateRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
