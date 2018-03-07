<?php

namespace JildertMiedema\LaravelPlupload;

use Illuminate\Support\ServiceProvider;

class LaravelPluploadServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('plupload', function ($app) {
            return $this->app->make(Manager::class, ['request' => $app['request']]);
        });
    }

    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        // asset publish
        $this->publishes([
            __DIR__.'/../../../public/assets' => public_path('vendor/jildertmiedema/laravel-plupload/'),
        ], 'assets');
        
        // config publish
        $this->publishes([
            __DIR__.'/../../../config/plupload.php' => config_path('plupload.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../../../config/plupload.php', 'plupload');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
