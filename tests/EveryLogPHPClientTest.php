<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use EveryLog\EveryLogPHPClient;

class EveryLogPHPClientTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = new EveryLogPHPClient();
    }

    public function testSetupWithOptions()
    {
        $options = [
            "api_key" => "1234567890",
            "projectId" => "my-project-id",
            "everylog_url" => "https://example.com/api/v1/log-entries",
        ];

        $this->client->setup($options);

        $this->assertEquals($options, $this->client->getOptions());
    }

    public function testSetupWithDefaults()
    {
        $this->client->setup();

        $this->assertEquals(EveryLogPHPClient::SETUP_DEFAULTS, $this->client->getOptions());
    }

    public function testNotifySendsRequestToEveryLogAPI()
{
    $mockClient = $this->getMockBuilder(Client::class)
        ->disableOriginalConstructor()
        ->getMock();

    $mockClient->expects($this->once())
        ->method('request')
        ->with(
            $this->equalTo('POST'),
            $this->equalTo('https://api.everylog.io/api/v1/log-entries'),
            $this->callback(function ($options) {
                $expectedHeaders = [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer test-api-key',
                    'charset' => 'utf-8',
                ];
                $this->assertSame($expectedHeaders, $options['headers']);
                $this->assertSame([], $options['json']['tags']);
                $this->assertSame('', $options['json']['link']);
                $this->assertFalse($options['json']['push']);
                $this->assertSame('', $options['json']['icon']);
                $this->assertSame([], $options['json']['externalChannels']);
                $this->assertSame([], $options['json']['groups']);
                $this->assertSame('test title', $options['json']['title']);
                $this->assertSame('test summary', $options['json']['summary']);
                $this->assertSame('test body', $options['json']['body']);
                $this->assertSame('test-project-id', $options['json']['projectId']);
                $this->assertSame((object)[], $options['json']['properties']);
                return true;
            })
        );

    $client = new EveryLogPHPClient();
    $client->setup([
        'api_key' => 'test-api-key',
        'projectId' => 'test-project-id',
    ]);
    $client->setHttpClient($mockClient);

    $client->notify([
        'title' => 'test title',
        'summary' => 'test summary',
        'body' => 'test body',
    ]);
}

}
