<?php

namespace spec\NiR\GhDashboard\Ingestion\Http;

use NiR\GhDashboard\Ingestion\Http\IngestEventResponse;
use PhpSpec\ObjectBehavior;

class IngestEventResponseSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedThrough('succeed');
        $this->shouldHaveType(IngestEventResponse::class);
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
