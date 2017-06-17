<?php

namespace spec\NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent;

use NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent\Responder;
use NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent\Response;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ResponderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Responder::class);
    }

    function it_returns_a_200_response_when_action_succeed(Response $response)
    {
        $response->hasSucceed()->willReturn(true);

        $this->__invoke($response)->shouldBeLike(new HttpResponse());
    }

    function it_returns_a_400_response_when_action_failed(Response $response)
    {
        $response->hasSucceed()->willReturn(false);

        $this->__invoke($response)->shouldBeLike(new HttpResponse('', 400));
    }
}
