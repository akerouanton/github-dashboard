<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Behat\ApiExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use NiR\GhDashboard\Behat\ApiExtension\Context\HttpClientAwareContext;

class HttpClientInitializer implements ContextInitializer
{
    /** @var HttpClient */
    private $client;

    /** @var RequestFactory */
    private $factory;

    /** @var string */
    private $baseUrl;

    public function __construct(HttpClient $client, RequestFactory $factory, string $baseUrl)
    {
        $this->client = $client;
        $this->factory = $factory;
        $this->baseUrl = $baseUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function initializeContext(Context $context)
    {
        if (!$context instanceof HttpClientAwareContext) {
            return;
        }

        $context->setClient($this->client);
        $context->setBaseUrl($this->baseUrl);
        $context->setRequestFactory($this->factory);
    }
}
