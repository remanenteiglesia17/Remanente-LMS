<?php

namespace App\Providers;

use App\Models\Tarea;
use App\Observers\TareaObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Tarea::observe(TareaObserver::class);
    }
}
