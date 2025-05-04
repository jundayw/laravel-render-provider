<?php

namespace Jundayw\Render\Contracts;

interface Renderable
{
    /**
     * 替换键值
     *
     * @param string $oldKey
     * @param string $newKey
     *
     * @return static
     */
    public function replace(string $oldKey, string $newKey): static;

    /**
     * 隐藏键值
     *
     * @param mixed $hides
     *
     * @return static
     */
    public function hide(mixed $hides): static;

    /**
     * 移除键值
     *
     * @param mixed $forgets
     *
     * @return static
     */
    public function forget(mixed $forgets): static;

    /**
     * 追加数据
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return static
     */
    public function with(string $key, mixed $value): static;

    /**
     * 重置对象
     *
     * @return static
     */
    public function reset(): static;

    /**
     * 刷新对象及宏
     *
     * @return static
     */
    public function flush(): static;

    /**
     * 批量赋值
     *
     * @param array $data   批量数据
     * @param bool  $append 追加数据模式
     *
     * @return static
     */
    public function data(array $data = [], bool $append = false): static;

    /**
     * 获取所有数据
     *
     * @param bool $hide
     *
     * @return array<string,mixed>
     */
    public function all(bool $hide = true): array;

    /**
     * 获取值
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key): mixed;

    /**
     * 数据响应
     *
     *
     * @param callable|null $response
     *
     * @return mixed
     */
    public function response(callable $response = null): mixed;
}
