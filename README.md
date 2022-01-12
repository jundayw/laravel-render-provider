# 安装方法

命令行下, 执行 composer 命令安装:
````
composer require jundayw/laravel-render-provider
````
authentication package that is simple and enjoyable to use.

# 对象方法

## 替换键值
replace(string $oldKey, string $newKey): $this
```php
$this->replace('message','msg');
```

## 隐藏键值
hidden(mixed $hiddens): $this
```php
$this->hidden('message');
$this->hidden('message','data');
$this->hidden(['message','data']);
```

## 移除键值
forget(mixed $forgets): $this
```php
$this->forget('message');
$this->forget('message','data');
$this->forget(['message','data']);
```

## 追加数据
with(string $key, mixed $value): $this
```php
$this->with('message','ok');
```

## 追加数据
withXxx($value): $this
```php
$this->withMsg('ok');
```

## 重置对象
reset(): $this
```php
$this->reset();
```

## 刷新对象及宏
flush(): $this
```php
$this->flush();
```

## 批量赋值
data(array $data = [], bool $append = false): $this
```php
$this->data(['message'=>'message','state'=>true]);
```

## 获取所有数据
all(bool $hidden = true): array
```php
$this->all();
$this->all(true);   // 隐藏键值已过滤
$this->all(false);  // 隐藏键值未过滤
```

## 获取值
get(string $key): mixed
```php
$this->get('message');
```

## 数据响应
response(?callable $response = null): mixed
```php
$this->response();
```

## JSON
json(?int $status = 200, ?array $headers = [], ?int $options = JSON_UNESCAPED_UNICODE): $this
```php
$this->json();
```

## JSONP
jsonp(?string $callback = 'jsonp', ?int $status = 200, ?array $headers = [], ?int $options = JSON_UNESCAPED_UNICODE): $this
```php
$this->jsonp();
```

## 宏：内置成功
success(?string $message = 'SUCCESS', ?string $url = null, mixed $data = null): $this
```php
$this->success();
```

## 宏：内置失败
error(?string $error = 'ERROR', ?string $url = null, mixed $errors = null): $this
```php
$this->error();
```

## 宏
macro($name, $macro): mixed
```php
Render::macro('sign',function($name){
    return $this->with($name,md5(http_build_query($this->all())));
});
// 获取签名数据
$data = Render::reset()->success('ok')->sign('token')->all();
// 响应数据
return Render::success('ok')->sign('token')->response();
```

# 使用场景

## 开箱即用
```php
return Render::success('ok', 'url...', 'data...')->response();
```
```json
{
    "state": true,
    "message": "ok",
    "url": "url...",
    "data": "data...",
    "timestamp": "2022-01-10T06:04:29Z"
}
```
```php
return Render::error('error', 'url...', 'data...')->response();
```
```json
{
    "state": false,
    "error": "error",
    "url": "url...",
    "errors": "data...",
    "timestamp": "2022-01-10T06:03:50Z"
}
```

## 替换键值
将响应数据中键值 timestamp 替换为 time
```php
return Render::success('success', 'url...', 'data...')
    ->replace('timestamp', 'time')
    ->response();
```
```json
{
    "state": true,
    "message": "ok",
    "url": "url...",
    "data": "data...",
    "time": "2022-01-10T06:09:21Z"
}
```

## 移除键值
若响应数据中键值 timestamp、url 不需要，可将其移除
```php
return Render::success('success', 'url...', 'data...')
    ->forget('timestamp', 'url')
    ->response();
```
```json
{
    "state": true,
    "message": "ok",
    "data": "data..."
}
```

## 追加数据
若响应数据中需要新增字段，可使用 with 方法
```php
return Render::success('success', 'url...', 'data...')
    ->with('appid', '...id...')
    ->with('appkey', '...key...')
    ->response();
```
```json
{
    "state": true,
    "message": "ok",
    "url": "url...",
    "data": "data...",
    "timestamp": "2022-01-10T06:15:08Z",
    "appid": "...id...",
    "appkey": "...key..."
}
```

## 隐藏键值
若响应数据中需要对敏感数据进行处理，可使用 hidden 方法
```php
return Render::success('success', 'url...', 'data...')
    ->with('appid', '...id...')
    ->with('appkey', '...key...')
    ->hidden('appkey')
    ->response();
```
```json
{
    "state": true,
    "message": "ok",
    "url": "url...",
    "data": "data...",
    "timestamp": "2022-01-10T06:20:04Z",
    "appid": "...id..."
}
```

## 扩展签名
将响应数据及 appid、appkey 进行签名，并且响应数据中不显示 appkey 字段
```php
Render::macro('sign', function($name) {
    $data = $this->all(false);// 获取所有数据包含隐藏字段 appkey
    return $this->with($name, md5(http_build_query($data)));// 数据签名方式可根据具体业务自定义
});
return Render::success('ok', 'url...', 'data...')
    ->with('appid', '...id...')
    ->with('appkey', '...key...')
    ->hidden('appkey')
    ->sign('token')
    ->response();
```
```json
{
    "state": true,
    "message": "ok",
    "url": "url...",
    "data": "data...",
    "timestamp": "2022-01-10T06:30:18Z",
    "appid": "...id...",
    "token": "f6ef314a3c1acd6e80f6e3b1858b6778"
}
```

# 响应场景

## 默认响应数据格式 json
```php
return Render::success('ok', 'url...', 'data...')
    ->json()
    ->response();
```
```json
{
    "state": true,
    "message": "ok",
    "url": "url...",
    "data": "data...",
    "timestamp": "2022-01-10T06:37:26Z"
}
```

## 响应数据格式 jsonp
```php
return Render::success('ok', 'url...', 'data...')
    ->jsonp()
    ->response();
```
```jsonp
jsonp({
    "state": true,
    "message": "ok",
    "url": "url...",
    "data": "data...",
    "timestamp": "2022-01-10T06:36:42Z"
});
```

## 扩展响应数据格式：宏方法扩展
```php
Render::macro('format', function(callable $callable){
    $this->format = function($data) use ($callable){
        return $callable($data);
    };
    return $this;
});
return Render::success('ok', 'url...', 'data...')
    ->format(function($data){
        return var_export($data, true);// 根据响应格式实现具体方法即可
    })
    ->response();
```
```php
array (
  'state' => true,
  'message' => 'ok',
  'url' => 'url...',
  'data' => 'data...',
  'timestamp' => '2022-01-10T06:49:45Z',
)
```

## 扩展响应数据格式：response 方法扩展
```php
return Render::success('ok', 'url...', 'data...')
    ->response(function($data){
        return var_export($data, true);
    });
```
```php
array (
  'state' => true,
  'message' => 'ok',
  'url' => 'url...',
  'data' => 'data...',
  'timestamp' => '2022-01-10T06:51:50Z',
)
```

# 宏场景

应用包已扩展 success/error 方法，若不适用业务场景，可通过 Render::flush() 方法格式化后自行定义。
```php
Render::macro('success', function(?string $message = 'SUCCESS', ?string $url = null, mixed $data = null) {
    $this->with('state', true);
    $this->with('message', $message);
    $this->with('url', $url);
    $this->with('data', $data);
    $this->with('timestamp', date('Y-m-d\TH:i:s\Z'));
    return $this;
});
Render::macro('error', function(?string $error = 'ERROR', ?string $url = null, mixed $errors = null) {
    $this->with('state', false);
    $this->with('error', $error);
    $this->with('url', $url);
    $this->with('errors', $errors);
    $this->with('timestamp', date('Y-m-d\TH:i:s\Z'));
    return $this;
});
```
推荐以上方法在服务提供者中扩展。

# 其他场景

## 批量赋值场景
```php
$data = [
    'state' => true,
    'message' => 'SUCCESS',
];
return Render::data($data)
    ->with('code', 200)
    ->response();
```
```json
{
    "state": true,
    "message": "SUCCESS",
    "code": 200
}
```

# 链式操作优先级

为防止目标数据与预期数据不一致，推荐链式操作优先级：
## 取值场景
```php
$render = Render::reset()       // 防止数据混淆
    ->data([], false)           // 批量覆盖模式
    //->success()->error()      // 方法优先级相同
    ->data([], true)            // 批量追加模式
    ->with('forget', 'forget')
    ->with('hidden', 'hidden')
    ->with('code', 200)
    ->forget('forget')->hidden('hidden')->replace('code', 'status');// 方法优先级相同

return $render->get('status');
return $render->all();
return $render
    ->json()->jsonp()           // 方法优先级相同
    ->response();
```

## 输出响应场景
```php
return Render::reset()          // 防止数据混淆
    ->data([], false)           // 批量覆盖模式
    //->success()->error()      // 方法优先级相同
    ->data([], true)            // 批量追加模式
    ->with('forget', 'forget')
    ->with('hidden', 'hidden')
    ->with('code', 200)
    ->forget('forget')->hidden('hidden')->replace('code', 'status')// 方法优先级相同
    ->json()->jsonp()           // 方法优先级相同
    ->response();               // response 为防止数据混淆，内部已经调用 reset() 方法
```