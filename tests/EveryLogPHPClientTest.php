<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use EveryLog\EveryLogPHPClient;

class EveryLogPHPClientTest extends TestCase
{
    public function testSetupWithOptions()
    {
        $client = new EveryLogPHPClient();
        $options = [
            "api_key" => "1234567890",
            "projectId" => "my-project-id",
            "everylog_url" => "https://example.com/api/v1/log-entries",
        ];

        $client->setup($options);

        $this->assertEquals($options, $client->getOptions());
    }

    public function testNotifySendsRequestToEveryLogAPI()
    {
        $jsonResponse = '{
            "accountId": "645d40f11150dc0f42694934",
            "body": "Test Body",
            "createdAt": "2023-05-15T05:13:45.160764528Z",
            "icon": "",
            "id": "6461bf891150dc0f42694959",
            "link": "",
            "projectId": "Testing-project-id",
            "properties": {"a": 123},
            "push": "",
            "starred": [],
            "summary": "Test Summary",
            "tags": [],
            "title": "Test Title",
            "updatedAt": "2023-05-15T05:13:45.160764797Z"
        }';

        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            $jsonResponse
        );

        $mockHandler = new MockHandler([$mockResponse]);
        $handlerStack = HandlerStack::create($mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);

        $client = new EveryLogPHPClient($httpClient);
        $client->setup([
            'api_key' => 'cec67dd9-a4d4-4519-bf7a-7c88c1b8a681',
            'projectId' => 'Testing-project-id',
        ]);
        $notifyOptions = [
            'title' => 'Test Title',
            'summary' => 'Test Summary',
            'body' => 'Test Body',
            'properties' => ['a' => 123],
        ];

        $response = $client->notify($notifyOptions);

        $expectedResult = json_decode($jsonResponse, true);

        $this->assertEquals($expectedResult, $response);
    }

    public function testNotifyWithEmptyApiKey()
    {

        $client = new EveryLogPHPClient();
        $client->setup(['projectId' => 'Testing-project-id']);
        $notifyOptions = [
            'title' => 'Test Title',
            'summary' => 'Test Summary',
            'body' => 'Test Body',
            'properties' => ['a' => 123],
        ];

        $expectedResult = ["message"=> "API Key or ProjectId is empty"];
        $response = $client->notify($notifyOptions);

        $this->assertEquals($expectedResult, $response);
    }
}