<?php

namespace App\Providers;
use App\Models\Message;
use App\Observers\MessageObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Register the MessageObserver for the Message model
        Message::observe(MessageObserver::class);
    }
}
