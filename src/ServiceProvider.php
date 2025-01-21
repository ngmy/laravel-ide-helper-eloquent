<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Ngmy\LaravelIdeHelperEloquent\Commands\GenerateCommand;

final class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateCommand::class,
            ]);
        }
    }
}
