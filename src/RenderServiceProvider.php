<?php

namespace Jundayw\Render;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Jundayw\Render\Contracts\Renderable;

class RenderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(Renderable::class, function (Application $app) {
            return tap($app->make(RenderFactory::class), function (RenderFactory $factory) {
                $factory->macro('success', function (?string $message = 'success', ?string $url = null, mixed $data = null) {
                    $this->with('state', true);
                    $this->with('message', $message);
                    $this->with('url', $url);
                    $this->with('data', $data);
                    $this->with('timestamp', date('Y-m-d\TH:i:s\Z'));
                    return $this;
                });
                $factory->macro('error', function (?string $error = 'error', ?string $url = null, mixed $errors = null) {
                    $this->with('state', false);
                    $this->with('error', $error);
                    $this->with('url', $url);
                    $this->with('errors', $errors);
                    $this->with('timestamp', date('Y-m-d\TH:i:s\Z'));
                    return $this;
                });
            });
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
