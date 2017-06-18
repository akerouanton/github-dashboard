<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Behat\ApiExtension\Context;

use Http\Client\HttpClient;
use Http\Message\RequestFactory;

trait HttpClientAwareTrait
{
    /** @var HttpClient|null */
    private $client;

    /** @var string|null */
    private $baseUrl;

    /** @var RequestFactory */
    private $requestFactory;

    public function setClient(HttpClient $client)
    {
        $this->client = $client;
    }

    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function setRequestFactory(RequestFactory $factory)
    {
        $this->requestFactory = $factory;
    }
}
