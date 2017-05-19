# 云之讯短信发送包

本包实现了云之讯的短信发送功能。

## 安装

通过 Composer 安装

``` bash
$ composer require "videni/sms-yun-zhi-xun"
```

## 使用方法

### 实例化短信平台类

```php
$provider = new YunZhiXun\Provider([
    'accountSid' => 'xxxxx',
    'authToken' => 'xxxxx',
    'appId' => 'xxxxxx',
]);
```

### 直接发送短信

```php
$message = Message::create()
    ->setRecipient('18800000000')
    ->setTemplateId('1')
    ->setData([
        '1',
        '2',
    ])->using($provider)
    ->send();
    
$message = Message::create([
    'recipient' => '18800000000',
    'template_id' => '1',
    'data' => [
        '1',
        '2',
    ],
])using($provider))->send();
```

### 判断短信是否发送成功

```php
if ($message->getStatus() == Huying\Sms\MessageStatus::STATUS_SENT) {
    echo '发送成功';
} else {
    echo '发送失败:错误码'.$message->getError()->getCode()
        .',错误消息:'.$message->getError()->getMessage();
}
```


## 许可协议

本项目使用 MIT 协议，详情请查看 [License File](LICENSE.md)。


