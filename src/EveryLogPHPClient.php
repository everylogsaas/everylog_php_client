<?php

namespace EveryLog;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class EveryLogPHPClient {
    public const SETUP_DEFAULTS = [
        "api_key" => null,
        "projectId" => null,
        "everylog_url" => "https://api.everylog.io/api/v1/log-entries",
    ];

    private const NOTIFY_DEFAULTS = [
        "title" => "Empty notification",
        "summary" => "Empty summary",
        "body" => "Empty body",
        "tags" => [],
        "link" => "",
        "push" => false,
        "icon" => "",
        "externalChannels" => [],
        "groups" => [],
    ];

    private $options;
    private $notifyOptions;

    public function __construct() {
        $this->options = self::SETUP_DEFAULTS;
        $this->notifyOptions = array_merge(self::NOTIFY_DEFAULTS, ["properties" => (object)[]]);
    }

    public function setup(array $options = []): self {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function getOptions() {
        return $this->options;
    }

    public function notify(array $notifyOptions = []): mixed {
        $this->notifyOptions = array_merge($this->notifyOptions, $notifyOptions);

        $mergedOptions = array_merge(["projectId" => $this->options["projectId"]], $this->notifyOptions);

        $headers = [
            "Content-Type" => "application/json",
            "Authorization" => "Bearer {$this->options['api_key']}",
            "charset" => "utf-8"
        ];

        $client = new Client();

        try {
            $response = $client->request('POST', $this->options["everylog_url"], [
                'headers' => $headers,
                'json' => $mergedOptions
            ]);

            $body = $response->getBody();

            return json_decode($body, true);
        } catch (RequestException $e) {
            return "Error message:\n\n" . $e;
        }
    }
    public function setHttpClient(): void
{
    $httpClient = $this->getMockBuilder(EveryLog\ClientInterface::class)
        ->getMock();

    $this->setHttpClient($httpClient);
}
}