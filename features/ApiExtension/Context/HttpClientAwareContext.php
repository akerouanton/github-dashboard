<?php

namespace NiR\GhDashboard\Behat\ApiExtension\Context;

use Behat\Behat\Context\Context;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;

interface HttpClientAwareContext extends Context
{
    public function setClient(HttpClient $client);

    public function setBaseUrl(string $baseUrl);

    public function setRequestFactory(RequestFactory $requestFactory);
}
