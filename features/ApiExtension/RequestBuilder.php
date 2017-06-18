<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Behat\ApiExtension;

use Http\Message\RequestFactory;

class RequestBuilder
{
    /** @var RequestFactory */
    private $factory;

    /** @var string */
    private $baseUrl = '';

    /** @var string */
    private $url = '';

    /** @var string */
    private $method = 'GET';

    /** @var array */
    private $headers = [];

    /** @var array */
    private $body = '';

    /**
     * @param RequestFactory $factory
     */
    public function __construct(RequestFactory $factory, string $baseUrl)
    {
        $this->factory = $factory;
        $this->baseUrl = $baseUrl;
    }

    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    public function setMethod(string $method)
    {
        $this->method = $method;

        return $this;
    }

    public function setBody(string $body)
    {
        $this->body = $body;

        return $this;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function addHeader(string $header, string $value)
    {
        $this->headers[$header] = $value;

        return $this;
    }

    public function build(): \Psr\Http\Message\RequestInterface
    {
        return $this->factory->createRequest(
            $this->method,
            rtrim($this->baseUrl, '/') . '/' . ltrim($this->url, '/'),
            $this->headers,
            $this->body
        );
    }
}
