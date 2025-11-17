<?php

declare(strict_types=1);

namespace Tumosa\LaravelConsoleOutput;

use Illuminate\Support\ServiceProvider;

class ConsoleOutputServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ConsoleOutput::class);
        $this->app->alias(ConsoleOutput::class, 'console-output');
    }
}