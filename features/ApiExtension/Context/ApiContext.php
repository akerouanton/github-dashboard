<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Behat\ApiExtension\Context;

use Behat\Behat\Context\Context;
use Http\Client\Exception\HttpException;
use NiR\GhDashboard\Behat\ApiExtension\RequestBuilder;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApiContext implements Context, HttpClientAwareContext
{
    use HttpClientAwareTrait;

    /** @var RequestBuilder|null */
    private $requestBuilder;

    /** @var ResponseInterface|null */
    private $response;

    public function getOrCreateRequestBuilder(): RequestBuilder
    {
        if ($this->requestBuilder === null) {
            $this->requestBuilder = new RequestBuilder($this->requestFactory, $this->baseUrl);
        }

        return $this->requestBuilder;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            $this->response = $this->client->sendRequest($request);
        } catch (HttpException $exception) {
            $this->response = $exception->getResponse();
        }

        return $this->response;
    }

    public function getResponse()
    {
        if ($this->response === null) {
            throw new \LogicException('No response found. You should send a request first.');
        }

        return $this->response;
    }

    /**
     * @Then I should receive a :statusCode response
     */
    public function iShouldReceiveAResponseWithStatusCode(int $statusCode)
    {
        Assert::assertEquals($statusCode, $this->response->getStatusCode());
    }
}
