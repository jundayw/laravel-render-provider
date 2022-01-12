<?php

namespace Jundayw\LaravelRenderProvider\Support\Factories;

use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Jundayw\LaravelRenderProvider\Support\Contracts\Factory;

class RenderFactory implements Factory
{
    use Macroable {
        __call as macroCall;
    }

    protected $attrs   = [];
    protected $hiddens = [];
    protected $forgets = [];
    protected $data    = [];
    protected $format;

    /**
     * 替换键值
     *
     * @param string $oldKey
     * @param string $newKey
     * @return RenderFactory
     *
     * @example $this->replace('message','msg')
     */
    public function replace(string $oldKey, string $newKey): RenderFactory
    {
        if (array_key_exists($oldKey, $this->attrs)) {
            $this->attrs[$oldKey] = $newKey;
        }

        return $this;
    }

    /**
     * 隐藏键值
     *
     * @param mixed $hiddens
     * @return RenderFactory
     *
     * @example $this->hidden('message')
     * @example $this->hidden('message','data')
     * @example $this->hidden(['message','data'])
     */
    public function hidden(mixed $hiddens): RenderFactory
    {
        $hiddens = is_array($hiddens) ? $hiddens : func_get_args();

        foreach ($hiddens as $hidden) {
            $this->hiddens[$hidden] = $hidden;
        }

        return $this;
    }

    /**
     * 移除键值
     *
     * @param mixed $forgets
     * @return RenderFactory
     *
     * @example $this->forget('message')
     * @example $this->forget('message','data')
     * @example $this->forget(['message','data'])
     */
    public function forget(mixed $forgets): RenderFactory
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
     * @param mixed $value
     * @return RenderFactory
     *
     * @example $this->with('message','ok')
     */
    public function with(string $key, mixed $value): RenderFactory
    {
        $this->attrs[$key] = $key;
        $this->data[$key]  = $value;

        return $this;
    }

    /**
     * 重置对象
     *
     * @return RenderFactory
     *
     * @example $this->reset()
     */
    public function reset(): RenderFactory
    {
        $this->attrs = $this->hiddens = $this->forgets = $this->data = [];

        return $this;
    }

    /**
     * 刷新对象及宏
     *
     * @return RenderFactory
     *
     * @example $this->flush()
     */
    public function flush(): RenderFactory
    {
        static::flushMacros();

        return $this->reset();
    }

    /**
     * 批量赋值
     *
     * @param array $data 批量数据
     * @param bool $append 追加数据模式
     * @return RenderFactory
     *
     * @example $this->data(['message'=>'message','state'=>true])
     */
    public function data(array $data = [], bool $append = false): RenderFactory
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
     * @return array<string,mixed>
     *
     * @example $this->all()
     * @example $this->all(true)
     * @example $this->all(false)
     */
    public function all(bool $hidden = true): array
    {
        return array_filter($this->build(), function($key) use ($hidden) {
            return ($hidden && in_array($key, $this->hiddens)) === false;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * 获取值
     *
     * @param string $key
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
     * @param string $name
     * @param array $arguments
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
     * @return mixed
     */
    public function response(?callable $response = null): mixed
    {
        $render = function($render) {
            return $render->format;
        };

        $format = $this->format ?? $render($this->json());

        return (function($callable) {
            $response = $this->all(true);
            $this->reset();
            return $callable($response);
        })($response ?? $format);
    }

    /**
     * @param int|null $status
     * @param array|null $headers
     * @param int|null $options
     * @return RenderFactory
     */
    public function json(?int $status = 200, ?array $headers = [], ?int $options = JSON_UNESCAPED_UNICODE): RenderFactory
    {
        $this->format = function($data) use ($status, $headers, $options) {
            return response()->json($data, $status, $headers, $options);
        };
        return $this;
    }

    /**
     * @param string|null $callback
     * @param int|null $status
     * @param array|null $headers
     * @param int|null $options
     * @return RenderFactory
     */
    public function jsonp(?string $callback = 'jsonp', ?int $status = 200, ?array $headers = [], ?int $options = JSON_UNESCAPED_UNICODE): RenderFactory
    {
        $this->format = function($data) use ($callback, $status, $headers, $options) {
            return response()->jsonp($callback, $data, $status, $headers, $options);
        };
        return $this;
    }

    /**
     * Throw a bad method call exception for the given method.
     *
     * @param string $method
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
