<?php

namespace Jundayw\LaravelRenderProvider;

use Illuminate\Support\ServiceProvider;

class RenderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('render',\Jundayw\LaravelRenderProvider\Support\Factories\RenderFactory::class);

        //$this->registerFacade();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function registerFacade()
    {
        $this->app->booting(function(){
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Render', \Jundayw\LaravelRenderProvider\Support\Facades\Render::class);
        });
    }
}
