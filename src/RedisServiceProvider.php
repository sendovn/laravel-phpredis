<?php

namespace TillKruss\LaravelPHPRedis;

use Cache;
use Illuminate\Support\Arr;
use Illuminate\Redis\RedisServiceProvider as ServiceProvider;

class RedisServiceProvider extends ServiceProvider
{
    /**
     * Register custom Redis cache driver.
     *
     * @return void
     */
    public function boot()
    {
        Cache::extend('redis', function ($app, $config) {
            $store = new RedisStore(
                $app['redis'],
                Arr::get($config, 'prefix', $this->app['config']['cache.prefix']),
                Arr::get($config, 'connection', 'default')
            );

            $repository = new Repository($store);

            if ($this->app->bound('Illuminate\Contracts\Events\Dispatcher')) {
                $repository->setEventDispatcher(
                    $this->app['Illuminate\Contracts\Events\Dispatcher']
                );
            }

            return $repository;
        });
    }

    /**
     * Register custom bindings.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('redis', function ($app) {
            return new Database($app['config']['database.redis']);
        });
    }
}
