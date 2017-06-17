<?php

namespace spec\NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent;

use NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent\Response;
use PhpSpec\ObjectBehavior;

class ResponseSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedThrough('succeed');
        $this->shouldHaveType(Response::class);
    }

    function it_has_succeed()
    {
        $this->beConstructedThrough('succeed');
        $this->hasSucceed()->shouldReturn(true);
    }

    function it_has_failed()
    {
        $this->beConstructedThrough('failed');
        $this->hasFailed()->shouldReturn(true);
    }
}
