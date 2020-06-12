<?php

namespace Jundayw\LaravelRenderProvider\Support\Factories;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class RenderFactory
{
    use Macroable {
        __call as macroCall;
    }

    protected $attrs = [];
    protected $hidden = [];
    protected $data = [];

    /**
     * 替换键值
     * $this->attr('message','msg')
     * @param $old
     * @param $new
     * @return $this
     */
    public function attr($old, $new)
    {
        $this->attrs[$old] = $new;
        return $this;
    }

    /**
     * 隐藏键值
     * $this->hidden(['msg','data']|'msg')
     * @param array $hidden
     * @return $this
     */
    public function hidden($hidden = [])
    {
        $hidden = is_array($hidden) ? $hidden : explode(',', $hidden);
        foreach ($hidden as $item) {
            $this->hidden[] = $item;
        }
        return $this;
    }

    /**
     * 追加数据
     * $this->with('message','ok')
     * @param $key
     * @param $value
     * @return $this
     */
    public function with($key, $value)
    {
        $this->attrs[$key] = $key;
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * 批量赋值
     * $this->data(['msg'=>'msg','state'=>true])
     * @param array $data
     * @return $this
     */
    public function data($data = [])
    {
        foreach ($data as $key => $item) {
            $this->with($key, $item);
        }

        return $this;
    }

    /**
     * 数据解析
     * @return $this
     */
    public function build()
    {
        $data = [];

        foreach ($this->attrs as $key => $item) {
            if (in_array($item, $this->hidden)) {
                continue;
            }
            $data[$item] = $this->data[$key] ?? null;
        }

        $this->data = $data;
        return $this;
    }

    /**
     * 获取值
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        $data = $this->all();
        return array_key_exists($key, $data) ? $data[$key] : null;
    }

    /**
     * 获取所有数据
     * @return array
     */
    public function all()
    {
        return $this->build()->data;
    }

    /**
     * 数据响应
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($status = 200, array $headers = [], $options = JSON_UNESCAPED_UNICODE)
    {
        return response()->json($this->all(), $status, $headers, $options);
    }

    /**
     * 监听宏
     * 新增with魔术方法
     * $this->withMsg('ok!')==$this->with('msg','ok!')
     * @param $name
     * @param $arguments
     * @return RenderFactory
     */
    public function __call($name, $arguments)
    {
        if (static::hasMacro($name)) {
            return $this->macroCall($name, $arguments);
        }
        if (Str::startsWith($name, 'with')) {
            $name = Str::substr($name, 4);
            $name = Str::lower($name);
            return $this->with($name, reset($arguments));
        }
    }

    /**
     * @param string $message
     * @param string $url
     * @param array $data
     * @return $this
     */
    public function success($message = 'SUCCESS', $url = '', $data = [])
    {
        $this->with('state', true);
        $this->with('message', $message);
        $this->with('url', $url);
        $this->with('data', $data);
        $this->with('timestamp', date('Y-m-d\Th:i:s\Z'));
        return $this;
    }

    /**
     * @param string $error
     * @param string $url
     * @param array $errors
     * @return $this
     */
    public function error($error = 'ERROR', $url = '', $errors = [])
    {
        $this->with('state', true);
        $this->with('error', $error);
        $this->with('url', $url);
        $this->with('errors', $errors);
        $this->with('timestamp', date('Y-m-d\Th:i:s\Z'));
        return $this;
    }
}
