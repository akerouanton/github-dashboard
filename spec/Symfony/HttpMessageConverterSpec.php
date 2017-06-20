<?php

namespace spec\NiR\GhDashboard\Symfony;

use NiR\GhDashboard\Symfony\HttpMessageConverter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpMessageConverterSpec extends ObjectBehavior
{
    function let(DiactorosFactory $psrFactory, HttpFoundationFactoryInterface $symfonyFactory)
    {
        $this->beConstructedWith($psrFactory, $symfonyFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(HttpMessageConverter::class);
    }

    function it_converts_a_symfony_request_into_a_psr7_compliant_request(
        Request $symfonyRequest,
        RequestInterface $psrRequest,
        $psrFactory
    ) {
        $psrFactory->createRequest($symfonyRequest)->willReturn($psrRequest);

        $this->convertRequest($symfonyRequest)->shouldReturn($psrRequest);
    }

    function it_converts_a_psr7_response_into_a_symfony_response(
        ResponseInterface $psrResponse,
        Response $symfonyResponse,
        $symfonyFactory
    ) {
        $symfonyFactory->createResponse($psrResponse)->willReturn($symfonyResponse);

        $this->convertResponse($psrResponse)->shouldReturn($symfonyResponse);
    }
}
