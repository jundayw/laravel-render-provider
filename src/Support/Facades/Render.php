<?php

namespace Jundayw\LaravelRenderProvider\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Jundayw\LaravelRenderProvider\Support\Factories\RenderFactory
 */
class Render extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'render';
    }
}
