<?php

namespace NiR\GhDashboard\Symfony;

use Psr\Http\Message\RequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class HttpMessageConverter
{
    /** @var DiactorosFactory */
    private $psrFactory;

    /** @var HttpFoundationFactory */
    private $symfonyFactory;

    public function __construct(
        HttpMessageFactoryInterface $psrFactory = null,
        HttpFoundationFactoryInterface $symfonyFactory = null
    ) {
        $this->psrFactory = $psrFactory ?: new DiactorosFactory();
        $this->symfonyFactory = $symfonyFactory ?: new HttpFoundationFactory();
    }

    public function convertRequest(Request $request): RequestInterface
    {
        return $this->psrFactory->createRequest($request);
    }

    public function convertResponse(ResponseInterface $response): Response
    {
        return $this->symfonyFactory->createResponse($response);
    }
}
