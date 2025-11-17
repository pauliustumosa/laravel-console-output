<?php

declare(strict_types=1);

namespace Tumosa\LaravelConsoleOutput\Facades;

use Illuminate\Support\Facades\Facade;

class ConsoleOutput extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'console-output';
    }
}