<?php

namespace spec\NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent;

use Http\Message\ResponseFactory;
use NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent\Responder;
use NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent\Response;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;

class ResponderSpec extends ObjectBehavior
{
    function let(ResponseFactory $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Responder::class);
    }

    function it_returns_a_200_response_when_action_succeed(Response $response, ResponseInterface $httpResponse, $factory)
    {
        $response->hasSucceed()->willReturn(true);
        $factory->createResponse(200)->willReturn($httpResponse);

        $this->__invoke($response)->shouldReturn($httpResponse);
    }

    function it_returns_a_400_response_when_action_failed(Response $response, ResponseInterface $httpResponse, $factory)
    {
        $response->hasSucceed()->willReturn(false);
        $factory->createResponse(400)->willReturn($httpResponse);

        $this->__invoke($response)->shouldReturn($httpResponse);
    }
}
