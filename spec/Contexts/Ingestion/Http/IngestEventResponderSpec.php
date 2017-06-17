<?php

namespace spec\NiR\GhDashboard\Contexts\Ingestion\Http;

use NiR\GhDashboard\Contexts\Ingestion\Http\IngestEventResponder;
use NiR\GhDashboard\Contexts\Ingestion\Http\IngestEventResponse;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Response;

class IngestEventResponderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IngestEventResponder::class);
    }

    function it_returns_a_200_response_when_action_succeed(IngestEventResponse $response)
    {
        $response->hasSucceed()->willReturn(true);

        $this->__invoke($response)->shouldBeLike(new Response());
    }

    function it_returns_a_400_response_when_action_failed(IngestEventResponse $response)
    {
        $response->hasSucceed()->willReturn(false);

        $this->__invoke($response)->shouldBeLike(new Response('', 400));
    }
}
