<?php

namespace Jundayw\Render;

use BadMethodCallException;
use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class RenderFactory implements Contracts\Renderable
{
    use Macroable {
        __call as macroCall;
    }

    protected array    $attrs   = [];
    protected array    $hides   = [];
    protected array    $forgets = [];
    protected array    $data    = [];
    protected ?Closure $format  = null;

    /**
     * 替换键值
     *
     * @param string $oldKey
     * @param string $newKey
     *
     * @return static
     *
     * @example $this->replace('message','msg')
     */
    public function replace(string $oldKey, string $newKey): static
    {
        if (array_key_exists($oldKey, $this->attrs)) {
            $this->attrs[$oldKey] = $newKey;
        }

        return $this;
    }

    /**
     * 隐藏键值
     *
     * @param mixed $hides
     *
     * @return static
     *
     * @example $this->hide('message')
     * @example $this->hide('message','data')
     * @example $this->hide(['message','data'])
     */
    public function hide(mixed $hides): static
    {
        $hides = is_array($hides) ? $hides : func_get_args();

        foreach ($hides as $hide) {
            $this->hides[$hide] = $hide;
        }

        return $this;
    }

    /**
     * 移除键值
     *
     * @param mixed $forgets
     *
     * @return static
     *
     * @example $this->forget('message')
     * @example $this->forget('message','data')
     * @example $this->forget(['message','data'])
     */
    public function forget(mixed $forgets): static
    {
        $forgets = is_array($forgets) ? $forgets : func_get_args();

        foreach ($forgets as $forget) {
            $this->forgets[$forget] = $forget;
        }

        return $this;
    }

    /**
     * 追加数据
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return static
     *
     * @example $this->with('message','ok')
     */
    public function with(string $key, mixed $value): static
    {
        $this->attrs[$key] = $key;
        $this->data[$key]  = $value;

        return $this;
    }

    /**
     * 重置对象
     *
     * @return static
     *
     * @example $this->reset()
     */
    public function reset(): static
    {
        $this->attrs = $this->hides = $this->forgets = $this->data = [];

        return $this;
    }

    /**
     * 刷新对象及宏
     *
     * @return static
     *
     * @example $this->flush()
     */
    public function flush(): static
    {
        static::flushMacros();

        return $this->reset();
    }

    /**
     * 批量赋值
     *
     * @param array $data   批量数据
     * @param bool  $append 追加数据模式
     *
     * @return static
     *
     * @example $this->data(['message'=>'message','state'=>true])
     */
    public function data(array $data = [], bool $append = false): static
    {
        if ($append === false) {
            $this->reset();
        }

        foreach ($data as $key => $item) {
            $this->with($key, $item);
        }

        return $this;
    }

    /**
     * 数据解析
     *
     * @return array<string,mixed>
     *
     * @example $this->build()
     */
    protected function build(): array
    {
        $data = [];

        foreach ($this->attrs as $key => $attr) {
            if (in_array($attr, $this->forgets)) {
                continue;
            }
            $data[$attr] = $this->data[$key] ?? null;
        }

        return $data;
    }

    /**
     * 获取所有数据
     *
     * @param bool $hidden
     *
     * @return array<string,mixed>
     *
     * @example $this->all()
     * @example $this->all(true)
     * @example $this->all(false)
     */
    public function all(bool $hidden = true): array
    {
        return array_filter($this->build(), function ($key) use ($hidden) {
            return !$hidden || !in_array($key, $this->hides);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * 获取值
     *
     * @param string $key
     *
     * @return mixed
     *
     * @example $this->get('message')
     */
    public function get(string $key): mixed
    {
        return array_key_exists($key, $data = $this->all(false)) ? $data[$key] : null;
    }

    /**
     * 监听宏
     * 新增with魔术方法
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     *
     * @example $this->withMsg('ok!')
     * @example $this->with('msg','ok!')
     */
    public function __call(string $method, array $arguments): mixed
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $arguments);
        }

        if (Str::startsWith($method, 'with')) {
            $name = Str::substr($method, 4);
            $name = Str::lower($name);
            return $this->with($name, reset($arguments));
        }

        static::throwBadMethodCallException($method);
    }

    /**
     * 数据响应
     *
     * @param callable|null $response
     *
     * @return mixed
     */
    public function response(?callable $response = null): mixed
    {
        return tap(Closure::bind(
            $response ?? $this->format ?? $this->json()->format,
            $this,
            static::class
        )($this->all()), fn() => $this->reset());
    }

    /**
     * @param int   $status
     * @param array $headers
     * @param int   $options
     *
     * @return static
     */
    public function json(int $status = 200, array $headers = [], int $options = JSON_UNESCAPED_UNICODE): static
    {
        $this->format = function ($data) use ($status, $headers, $options) {
            return response()->json($data, $status, $headers, $options);
        };
        return $this;
    }

    /**
     * @param string $callback
     * @param int    $status
     * @param array  $headers
     * @param int    $options
     *
     * @return static
     */
    public function jsonp(string $callback = 'jsonp', int $status = 200, array $headers = [], int $options = JSON_UNESCAPED_UNICODE): static
    {
        $this->format = function ($data) use ($callback, $status, $headers, $options) {
            return response()->jsonp($callback, $data, $status, $headers, $options);
        };
        return $this;
    }

    /**
     * Throw a bad method call exception for the given method.
     *
     * @param string $method
     *
     * @return void
     *
     * @throws BadMethodCallException
     */
    protected static function throwBadMethodCallException(string $method): void
    {
        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }
}
