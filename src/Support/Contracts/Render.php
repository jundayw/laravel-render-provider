<?php

namespace Jundayw\LaravelRenderProvider\Support\Contracts;

interface Render
{
    /**
     * 替换键值
     *
     * @param string $oldKey
     * @param string $newKey
     * @return $this
     */
    public function replace(string $oldKey, string $newKey);

    /**
     * 隐藏键值
     *
     * @param mixed $hiddens
     * @return $this
     */
    public function hidden(mixed $hiddens);

    /**
     * 移除键值
     *
     * @param mixed $forgets
     * @return $this
     */
    public function forget(mixed $forgets);

    /**
     * 追加数据
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function with(string $key, mixed $value);

    /**
     * 重置对象
     * 
     * @return $this
     */
    public function reset();

    /**
     * 刷新对象及宏
     * 
     * @return $this
     */
    public function flush();

    /**
     * 批量赋值
     *
     * @param array $data 批量数据
     * @param bool $append 追加数据模式
     * @return $this
     */
    public function data(array $data = [], bool $append = false);

    /**
     * 获取所有数据
     *
     * @param bool $hidden
     * @return array<string,mixed>
     */
    public function all(bool $hidden = true);

    /**
     * 获取值
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * 数据响应
     *
     * @param callable|null $response
     * @return mixed
     */
    public function response(?callable $response = null);
}
