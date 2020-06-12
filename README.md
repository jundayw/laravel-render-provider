# 安装方法
命令行下, 执行 composer 命令安装:
````
composer require jundayw/laravel-render-provider
````

# 使用方法
authentication package that is simple and enjoyable to use.

## 替换键值
attr($old, $new)
```
$this->attr('message','msg')
```

## 隐藏键值
hidden($hidden = [])
```
$this->hidden(['msg','data']|'msg')
```

## 追加数据
with($key, $value)
```
$this->with('message','ok')
```

## 追加数据
withXxx($value)
```
$this->withMsg('ok')
```

## 批量赋值
data($data = [])
```
$this->data(['msg'=>'msg','state'=>true])
```

## 获取值
get($key)
```
$this->get('msg')
```

## 获取所有数据
all()
```
$this->all()
```

## 数据响应
response($status = 200, array $headers = [], $options = JSON_UNESCAPED_UNICODE)
```
$this->response()
```

## 内置成功
success($message = 'SUCCESS', $url = '', $data = [])
```
$this->success()
```

## 内置失败
error($error = 'ERROR', $url = '', $errors = [])
```
$this->error()
```

## 宏
macro($name, $macro)
```
Render::macro('sign',function($name){
    return $this->with($name,md5(http_build_query($this->all())));
});
// 获取签名数据
$data = Render::success('ok')->sign('token')->all();
// 响应数据
return Render::success('ok')->sign('token')->response();
```
