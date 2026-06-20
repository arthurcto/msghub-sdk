<?php

namespace MsgHub\Laravel;

use Illuminate\Support\ServiceProvider;
use MsgHub\MsgHubClient;

class MsgHubServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/msghub.php', 'msghub');

        $this->app->singleton(MsgHubClient::class, function ($app) {
            $config = $app['config']['msghub'];

            return new MsgHubClient(
                baseUrl: $config['url'],
                apiKey:  $config['api_key'],
                timeout: (int) $config['timeout'],
            );
        });

        // Alias para resolução pelo nome curto
        $this->app->alias(MsgHubClient::class, 'msghub');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/msghub.php' => config_path('msghub.php'),
            ], 'msghub-config');
        }
    }
}
