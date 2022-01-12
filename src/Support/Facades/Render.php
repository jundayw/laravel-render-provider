<?php

namespace Jundayw\LaravelRenderProvider\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Traits\Macroable;
use Jundayw\LaravelRenderProvider\Support\Contracts\Factory;
use Jundayw\LaravelRenderProvider\Support\Factories\RenderFactory;

/**
 * @method static RenderFactory replace(string $oldKey, string $newKey)
 * @method static RenderFactory hidden(mixed $hiddens)
 * @method static RenderFactory forget(mixed $forgets)
 * @method static RenderFactory with(string $key, mixed $value)
 * @method static RenderFactory reset()
 * @method static RenderFactory flush()
 * @method static RenderFactory data(array $data = [], bool $append = false)
 * @method static array all(bool $hidden = true)
 * @method static mixed get(string $key)
 * @method static mixed response(?callable $response = null)
 * @method static RenderFactory json(?int $status = 200, ?array $headers = [], ?int $options = JSON_UNESCAPED_UNICODE)
 * @method static RenderFactory jsonp(?string $callback = 'jsonp', ?int $status = 200, ?array $headers = [], ?int $options = JSON_UNESCAPED_UNICODE)
 * @method static RenderFactory success(?string $message = 'SUCCESS', ?string $url = null, mixed $data = null)
 * @method static RenderFactory error(?string $error = 'ERROR', ?string $url = null, mixed $errors = null)
 *
 * @method static void macro($name, $macro)
 * @method static void mixin($mixin, $replace = true)
 * @method static bool hasMacro($name)
 * @method static void flushMacros()
 *
 * @see RenderFactory
 * @see Macroable
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
        return Factory::class;
    }
}
