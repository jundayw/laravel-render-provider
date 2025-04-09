<?php

namespace Jundayw\Render;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Jundayw\Render\Contracts\Renderable;

class RenderServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(Renderable::class, RenderFactory::class);
    }

    /**
     * Bootstrap services.
     *
     * @param RenderFactory $factory
     *
     * @return void
     */
    public function boot(RenderFactory $factory): void
    {
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
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [Renderable::class];
    }
}
