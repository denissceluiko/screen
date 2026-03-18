<?php

namespace App\Providers;

use App\Services\DockerService;
use App\Services\Scanners\YaraScanner;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DockerService::class, fn () => new DockerService(
            socketPath: config('services.yara.socket', '/var/run/docker.sock'),
        ));

        $this->app->singleton(YaraScanner::class, fn ($app) => new YaraScanner(
            docker: $app->make(DockerService::class),
        ));
    }

    public function boot(): void
    {
        //
    }
}
