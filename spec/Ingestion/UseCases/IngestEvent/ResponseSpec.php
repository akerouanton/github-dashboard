<?php

namespace spec\NiR\GhDashboard\Ingestion\UseCases\IngestEvent;

use NiR\GhDashboard\Ingestion\UseCases\IngestEvent\Response;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResponseSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Response::class);
    }

    function it_represents_a_succesful_ingestion()
    {
        $this->beConstructedThrough('succeeded');

        $this->hasErrors()->shouldReturn(false);
    }

    function it_represents_a_failed_ingestion()
    {
        $this->beConstructedThrough('failed', [['error1', 'error2']]);

        $this->hasErrors()->shouldReturn(true);
        $this->getErrors()->shouldReturn(['error1', 'error2']);
    }
}
