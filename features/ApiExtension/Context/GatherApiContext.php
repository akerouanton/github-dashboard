<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Behat\ApiExtension\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use NiR\GhDashboard\Behat\ApiExtension\RequestBuilder;

trait GatherApiContext
{
    /** @var ApiContext */
    private $apiContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->apiContext = $environment->getContext(ApiContext::class);
    }

    private function getOrCreateRequestBuilder(): RequestBuilder
    {
        return $this->apiContext->getOrCreateRequestBuilder();
    }
}
