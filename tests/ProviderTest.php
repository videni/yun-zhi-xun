<?php

namespace YunZhiXun\Test;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Huying\Sms\Message;
use Huying\Sms\MessageStatus;
use YunZhiXun\Provider;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructFailed()
    {
        $provider = new Provider([
            'accountSid' => 'test',
            'authToken' => 'test',
        ]);
    }

    public function testGetName()
    {
        $provider = new Provider([
            'accountSid' => 'test',
            'authToken' => 'test',
            'appId' => 'test',
        ]);

        $this->assertEquals('yun_zhi_xun', $provider->getName());
    }

    public function testProviderSendTemplateMessageSuccessfully()
    {
        $options = [
            'accountSid' => 'test_sid',
            'authToken' => 'test_token',
            'appId' => 'test_app_id',
        ];

        $responseData = <<<JSON
{
 "resp"        : {
    "respCode"    : "000000",
    "failure"     : 1,
    "templateSMS" : {
        "createDate"  : 20140623185016,
        "smsId"       : "f96f79240e372587e9284cd580d8f953"
        }
    }
}
JSON;

        $mock = new MockHandler([
            new Response(200, [], $responseData),
        ]);
        $handler = HandlerStack::create($mock);
        $httpClient = new HttpClient(['handler' => $handler]);
        $message = Message::create()
            ->setRecipient('18800000000')
            ->setTemplateId('123456')
            ->setData([
                '4585',
                '15'
            ]);

        $provider = new Provider($options, [
            'httpClient' => $httpClient,
        ]);
        $message->using($provider)->send();

        $this->assertEquals(MessageStatus::STATUS_SENT, $message->getStatus());
    }


    public function testProviderWithRealOptions()
    {
        if (empty(getenv('ACCOUNT_SID'))) {
            $this->markTestSkipped('You need to configure the app parameters in phpunit.xml');
        }

        $providerOptions = [
            'accountSid' => getenv('ACCOUNT_SID'),
            'authToken' => getenv('AUTH_TOKEN'),
            'appId' => getenv('APP_ID'),
        ];
        $httpClient = new HttpClient();
        $message = Message::create()
            ->setRecipient(getenv('TELEPHONE'))
            ->setTemplateId(getenv('TEMPLATE_ID'))
            ->setData([
                rand(100000, 999999),
                rand(1, 100),
            ]);

        $provider = new Provider($providerOptions, [
            'httpClient' => $httpClient,
        ]);
        $message->using($provider)->send();

        $this->assertEquals(MessageStatus::STATUS_SENT, $message->getStatus());
    }


    public function testProviderSendTemplateMessageFailed()
    {
        $option = [
            'accountSid' => 'test_sid',
            'authToken' => 'test_token',
            'appId' => 'test_app_id',
        ];

        $responseData = <<<JSON
{
 "resp"        : {
    "respCode"    : "000001",
    "failure"     : 1,
    "templateSMS" : {
        "createDate"  : 20140623185016,
        "smsId"       : "f96f79240e372587e9284cd580d8f953"
        }
    }
}
JSON;
        $mock = new MockHandler([
            new Response(200, [], $responseData),
        ]);
        $handler = HandlerStack::create($mock);
        $httpClient = new HttpClient(['handler' => $handler]);
        $message = Message::create()
            ->setRecipient('18800000000')
            ->setTemplateId('123456')
            ->setData([
                '4585',
                '15'
            ]);


        $provider = new Provider($option, [
            'httpClient' => $httpClient,
        ]);
        $message->using($provider)->send();

        $this->assertEquals(MessageStatus::STATUS_FAILED, $message->getStatus());
    }
}
