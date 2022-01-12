<?php

namespace Jundayw\LaravelRenderProvider;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Jundayw\LaravelRenderProvider\Support\Contracts\Factory;
use Jundayw\LaravelRenderProvider\Support\Facades\Render;
use Jundayw\LaravelRenderProvider\Support\Factories\RenderFactory;

class RenderServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Factory::class, RenderFactory::class);
        // $this->app->bind('render', Factory::class);
        // PackageManifest loaded from composer.json of extra.laravel.aliases
        //$this->registerFacade();
    }

    /**
     * Register Factories.
     *
     * @return void
     */
    public function registerFacade()
    {
        $this->app->booting(function() {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Render', Render::class);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Factory $factory)
    {
        $factory->macro('success', function(?string $message = 'SUCCESS', ?string $url = null, mixed $data = null) {
            $this->with('state', true);
            $this->with('message', $message);
            $this->with('url', $url);
            $this->with('data', $data);
            $this->with('timestamp', date('Y-m-d\TH:i:s\Z'));
            return $this;
        });
        $factory->macro('error', function(?string $error = 'ERROR', ?string $url = null, mixed $errors = null) {
            $this->with('state', false);
            $this->with('error', $error);
            $this->with('url', $url);
            $this->with('errors', $errors);
            $this->with('timestamp', date('Y-m-d\TH:i:s\Z'));
            return $this;
        });
    }

    public function provides()
    {
        return [Factory::class];
    }
}
