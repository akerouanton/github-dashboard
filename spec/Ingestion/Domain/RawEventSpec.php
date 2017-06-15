<?php

namespace spec\NiR\GhDashboard\Ingestion\Domain;

use NiR\GhDashboard\Ingestion\Domain\RawEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RawEventSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('01234', 'NiR/GhDashboard', 'issue', ['foo' => 'bar'], new \DateTimeImmutable());
        $this->shouldHaveType(RawEvent::class);
    }

    function it_throws_an_exception_if_id_is_empty()
    {
        $this->beConstructedWith('', 'NiR/GhDashboard', 'issue', ['foo' => 'bar'], new \DateTimeImmutable());
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_an_exception_if_repo_name_is_empty()
    {
        $this->beConstructedWith('01234', '', 'issue', ['foo' => 'bar'], new \DateTimeImmutable());
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_an_exception_if_event_type_is_empty()
    {
        $this->beConstructedWith('01234', 'NiR/GhDashboard', '', ['foo' => 'bar'], new \DateTimeImmutable());
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_an_exception_if_payload_is_empty()
    {
        $this->beConstructedWith('01234', 'NiR/GhDashboard', 'issue', [], new \DateTimeImmutable());
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}
