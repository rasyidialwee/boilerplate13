<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelSetProvider;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withCache(__DIR__.'/storage/rector')
    ->withSkip([
        __DIR__.'/vendor',
        __DIR__.'/storage',
        __DIR__.'/bootstrap/cache',
        __DIR__.'/node_modules',
    ])
    ->withSetProviders(LaravelSetProvider::class)
    ->withComposerBased(laravel: true);
